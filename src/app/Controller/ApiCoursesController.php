<?php

App::uses('AppController', 'Controller');

class ApiCoursesController extends AppController {

    public $uses = array('Course', 'Tag', 'CourseTag', 'Chapter');
    public $components = array();
    

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $this->autoRender = false;
        $this->response->type('json');
    }

    public function api_index() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        $query = $this->request->query;
        $conditions = [];
        if(isset($query['tag_id']) && $query['tag_id'] != ''){
            $ids = [];
            $tag_ids = explode(",", $query['tag_id']);
            $tags = $this->CourseTag->find('all', [
                'contain' => false,
                'conditions' => [
                    'CourseTag.tag_id' => $tag_ids
                ]
            ]);
            
            $group_tags = [];
            if(!empty($tags)){
                foreach ($tags as $tag) {
                    if(!isset($group_tags[$tag['CourseTag']['tag_id']])){
                        $group_tags[$tag['CourseTag']['tag_id']] = [];
                    }
                    $group_tags[$tag['CourseTag']['tag_id']][] = $tag['CourseTag']['course_id'];
                }
            }

            if(count($group_tags) != count($tag_ids)){
                return $this->response->body(json_encode($response));
            }
            
            if(!empty($group_tags)){
                $count = 0;
                foreach ($group_tags as $tag_id => $course_ids) {
                    if($count == 0){
                        $ids = $course_ids;
                    } else {
                        $ids = array_intersect($ids, $course_ids);
                    }
                    $count++;
                }
                
            }
            $conditions += ['Course.id' => $ids];
        }
        
        if(isset($query['featured'])){
            $conditions += ['Course.featured' => $query['featured']];
        }
        if(isset($query['style'])){
            $conditions += ['Course.style' => $query['style']];
        }
        
        if(!isset($query['limit'])){
            $query['limit'] = 20;
        }
        if(!isset($query['page']) || $query['page'] < 1){
            $query['page'] = 1;
        }
        $items = $this->Course->find('all', [
            'contain' => ['CourseTag' => 'Tag', 'Lesson' => [
                'LessonTag' => 'Tag',
                'limit' => 3
                ]],
            'conditions' => $conditions,
            'order' => ['sort_order' => 'DESC'],
            'limit' => $query['limit'],
            'page' => $query['page'],
        ]);
        if(!empty($items)){
            foreach($items as $item){
                $row = $item['Course'];
                $row['tags'] = $this->_getTagObjects($item['CourseTag']);
                $row['lessons'] = $this->_getLessionObjects($item['Lesson']);
                $response->data[] = $row;
            }
        }
        return $this->response->body(json_encode($response));
    }

    public function api_detail() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $id = $this->params['id'];
        $item = $this->Course->find('first', [
            'contain' => ['Chapter' => ['Lesson' => ['LessonTag' => 'Tag']], 'CourseTag' => 'Tag'],
            'conditions' => [
                'Course.id' => $id
            ]
        ]);
        if(!empty($item)){
            foreach ($item as $key => $value) {
                $response->data->id = $item['Course']['id'];
                $response->data->title = $item['Course']['title'];
                $response->data->thumb = $item['Course']['thumb'];
                $response->data->short_description = $item['Course']['short_description'];
                $response->data->description = $item['Course']['description'];
                $response->data->tags = $this->_getTagObjects($item['CourseTag']);
                $response->data->chapters = [];
                if(!empty($item['Chapter'])){
                    foreach ($item['Chapter'] as $chapter) {
                        if(empty($chapter)){
                            continue;
                        }
                        $row = [
                            'id' => $chapter['id'],
                            'title' => $chapter['title'],
                        ];
                        $row['lessons'] = $this->_getLessionObjects($chapter['Lesson']);
                        $response->data->chapters[] = $row;
                    }
                }
            }
        }
        return $this->response->body(json_encode($response));
    }
    
    public function api_tags(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        
        $query = $this->request->query;
        if(!isset($query['limit'])){
            $query['limit'] = 10;
        }
        if(!isset($query['page']) || $query['page'] < 1){
            $query['page'] = 1;
        }
        $conditions = [];
        if(isset($query['popular']) && $query['popular'] == 1){
            $conditions += [
                'Tag.popular > ' => 0
            ];
        }
        $tags = $this->Tag->find('all', [
            'conditions' => $conditions,
            'fields' => ['id', 'name', 'popular'],
            'limit' => $query['limit'],
            'page' => $query['page'],
            'order' => ['Tag.popular' => 'DESC', 'Tag.id' => 'DESC']
        ]);
        
        if(!empty($tags)){
            foreach ($tags as $tag) {
                $response->data[] = $tag['Tag'];
            }
        }
        
        return $this->response->body(json_encode($response));
    }

}
