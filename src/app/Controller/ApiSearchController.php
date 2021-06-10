<?php

App::uses('AppController', 'Controller');

class ApiSearchController extends AppController {

    public $uses = array('Course', 'Lesson', 'Unit', 'CourseTag', 'LessonTag',
        'Teacher', 'UserTeacher');
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
        $keyword = isset($query['keyword']) ? $query['keyword'] : '';
        $tags = isset($query['tag_id']) ? $query['tag_id'] : '';
        $type = isset($query['type']) ? $query['type'] : 'all';
        if(!isset($query['limit']) || $query['limit'] <= 0){
            $query['limit'] = 20;
        }
        if(!isset($query['page']) || $query['page'] <= 0){
            $query['page'] = 1;
        }
        switch($type){
            case 'all':
                $response->data['courses'] = $this->searchCourses($keyword, $tags, 5, 1);
                $response->data['lessons'] = $this->searchLessons($keyword, $tags, 5, 1);
                $response->data['units'] = $this->searchUnits($keyword, 5, 1);
                break;
            case 'course':
                $response->data = $this->searchCourses($keyword, $tags, $query['limit'], $query['page']);
                break;
            case 'lesson':
                $response->data = $this->searchLessons($keyword, $tags, $query['limit'], $query['page']);
                break;
            case 'unit':
                $response->data = $this->searchUnits($keyword, $query['limit'], $query['page']);
                break;
            case 'teacher':
                $response->data = $this->searchTeachers($keyword, $query['limit'], $query['page']);
                break;
            default:
                $response->data['courses'] = $this->searchCourses($keyword, $tags, 5, 1);
                $response->data['lessons'] = $this->searchLessons($keyword, $tags, 5, 1);
                $response->data['units'] = $this->searchUnits($keyword, 5, 1);
                break;
        }
        return $this->response->body(json_encode($response));
    }
    
    private function searchCourses($keyword, $tags, $limit, $page){
        $conditions = [
            'OR' => [
                'Course.title LIKE ' => '%' . $keyword . '%',
                'Course.short_description LIKE ' => '%' . $keyword . '%',
            ]
        ];
        if($tags != ''){
            $ids = [];
            $tag_ids = explode(",", $tags);
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
                return [];
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
        $items = $this->Course->find('all', [
            'contain' => ['CourseTag' => 'Tag'], 
            'conditions' => $conditions,
            'page' => $page,
            'limit' => $limit,
            'order' => ['Course.sort_order' => 'DESC']
        ]);
        $result = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $row = $item['Course'];
                $row['tags'] = $this->_getTagObjects($item['CourseTag']);
                $result[] = $row;
            }
        }
        return $result;
    }
    
    private function searchLessons($keyword, $tags, $limit, $page){
        $conditions = [
            'OR' => [
                'Lesson.title LIKE ' => '%' . $keyword . '%',
                'Lesson.short_description LIKE ' => '%' . $keyword . '%',
            ]
        ];
        
        if($tags != ''){
            $ids = [];
            $tag_ids = explode(",", $tags);
            $tags = $this->LessonTag->find('all', [
                'contain' => false,
                'conditions' => [
                    'LessonTag.tag_id' => $tag_ids
                ]
            ]);

            $group_tags = [];
            if(!empty($tags)){
                foreach ($tags as $tag) {
                    if(!isset($group_tags[$tag['LessonTag']['tag_id']])){
                        $group_tags[$tag['LessonTag']['tag_id']] = [];
                    }
                    $group_tags[$tag['LessonTag']['tag_id']][] = $tag['LessonTag']['lesson_id'];
                }
            }
            if(!empty($group_tags)){
                $count = 0;
                foreach ($group_tags as $tag_id => $lesson_ids) {
                    if($count == 0){
                        $ids = $lesson_ids;
                    } else {
                        $ids = array_intersect($ids, $lesson_ids);
                    }
                    $count++;
                }

            }
            $conditions += ['Lesson.id' => $ids];
        }
        
        $items = $this->Lesson->find('all', [
            'contain' => ['LessonTag' => 'Tag'], 
            'conditions' => $conditions,
            'page' => $page,
            'limit' => $limit,
            'order' => ['Lesson.sort_order' => 'DESC']
        ]);
        $result = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $row = $item['Lesson'];
                $row['tags'] = $this->_getTagObjects($item['LessonTag']);
                $result[] = $row;
            }
        }
        return $result;
    }
    
    private function searchUnits($keyword, $limit, $page){
        $items = $this->Unit->find('all', [
            'conditions' => [
                'Unit.title LIKE ' => '%' . $keyword . '%',
            ],
            'page' => $page,
            'limit' => $limit,
            'order' => ['Unit.sort_order' => 'DESC']
        ]);
        $result = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $row = $item['Unit'];
                $result[] = $row;
            }
        }
        return $result;
    }
    
    private function searchTeachers($keyword, $limit, $page){
        $user = $this->getAuthUser();
        $items = $this->Teacher->find('all', [
            'contain' => ['TeacherSkill' => [
                    'fields' => ['id', 'name', 'sort_order', 'lesson_count'],
                    'order' => ['sort_order' => 'DESC']
                    ]],
            'conditions' => [
                'OR' => [
                    'Teacher.name LIKE ' => '%' . $keyword . '%',
                    'Teacher.email LIKE ' => '%' . $keyword . '%',
                ]
            ],
            'page' => $page,
            'limit' => $limit,
            'order' => ['Teacher.name' => 'ASC']
        ]);
        $result = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $item['Teacher']['skills'] = $item['TeacherSkill'];
                $row = $item['Teacher'];
                if($user){
                    $row['is_followed'] = $this->isFollowed($user['id'], $row['id']);
                } else {
                    $row['is_followed'] = 0;
                }
                $result[] = $row;
            }
        }
        return $result;
    }
}
