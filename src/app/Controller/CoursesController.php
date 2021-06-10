<?php

App::uses('AppController', 'Controller');

class CoursesController extends AppController {

    public $uses = array('Course', 'CourseTag', 'Chapter');
    public $components = array('CRUD', 'ExtendControl', 'Filter', 'S3');
    
    public $paginate = array(
        'limit' => 10,
        'maxLimit' => 500
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function isAuthorized($user) {
        if ($user['type'] == 'admin') {
            return true;
        }
        return false;
    }

    public function admin_index() {
        $this->getTags();
        $this->set('title_for_layout', __('List courses'));
        $this->set('title', __('Courses management'));
        $this->set('small_title', __('Courses'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List courses'), 'slug' => '#'));
        
        $conditions = array();
        $keyword = '';
        $tag_id = '';
        $limit_page = $this->paginate['limit'];
        $filters = $this->Filter->get_filters();
        if (!empty($filters)) {
            $this->paginate['order'] = array($filters['order'] => $filters['order_by']);
        }
        $flag = false;
        if ($this->request->query) {
            $flag = true;
            $data = $this->request->query;
            
            if (isset($data['keyword'])) {
                if (!empty($data['keyword'])) {
                    $conditions += array('Course.title LIKE ' => '%' . $data['keyword'] . '%');
                }
                $keyword = $data['keyword'];
            }

            if (isset($data['tag_id'])) {
                if($data['tag_id'] != ''){
                    $course_ids = $this->getCoursesByTag($data['tag_id']);
                    $conditions += ['Course.id' => $course_ids];
                    $tag_id = $data['tag_id'];
                }
            }
            if (isset($data['limit_page'])) {
                $this->paginate['limit'] = $data['limit_page'];
                $this->paginate['maxLimit'] = $data['limit_page'];
                $limit_page = $data['limit_page'];
            }
            if (isset($data['order'])) {
                $this->paginate['order'] = array($data['order'] => $data['order_by']);
            }
        }
        if (!isset($this->paginate['order'])) {
            $this->paginate['order'] = ['sort_order' => 'DESC', 'id' => 'DESC'];
        }

        $this->Paginator->settings = $this->paginate;
        $items = $this->Paginator->paginate('Course', $conditions);
        
        $this->set('keyword', $keyword);
        $this->set('tag_id', $tag_id);
        $this->set('flag', $flag);
        $this->set('limit_page', $limit_page);
        $this->set('items', $items);
        $this->set('query', array(
            'keyword' => $keyword,
            'tag_id' => $tag_id,
            'flag' => $flag,
            'limit_page' => $limit_page,
        ));
    }

    public function admin_detail($id) {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Course\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Courses'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Course\'s detail'), 'slug' => '#'));
        
        $item = $this->Course->find('first', [
            'contain' => ['Chapter', 'CourseTag' => 'Tag'],
            'conditions' => [
                'Course.id' => $id
            ]
        ]);
        $this->set('item', $item);
        $this->request->data = $item;
        $this->request->data['_method'] = 'GET';
        
        $tags = [];
        if(!empty($item['CourseTag'])){
            foreach($item['CourseTag'] as $tag){
                if(!empty($tag['Tag'])){
                    $tags[] = '#' . $tag['Tag']['name'];
                }
            }
        }
        $tags = implode(', ', $tags);
        $this->set('tags', $tags);
    }

    public function admin_delete($id = null, $cascade = true) {
        //var_dump($cascade); die();
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Course, $cascade ? true : false);
    }

    public function admin_delete_checked() {
        $this->CRUD->delete_checked($this->Course, true, 'index');
    }

    public function admin_edit($id = null) {
        $this->getTags();
        $this->set('title', __('Courses'));
        $this->set('small_title', __('Edit Course'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Courses'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Course'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Course, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
                if ($thumb) {
                    $this->request->data['Course']['thumb'] = $thumb;
                }

                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    $data = $this->request->data;
                    // Update tags
                    $oldTags = [];
                    $oldChapters = [];
                    if(!empty($item['CourseTag'])){
                        foreach($item['CourseTag'] as $tag){
                            $oldTags[] = $tag['tag_id'];
                        }
                    }
                    
                    if(!empty($item['Chapter'])){
                        foreach($item['Chapter'] as $chapter){
                            $oldChapters[$chapter['id']] = $chapter['title'];
                        }
                    }
                    $this->updateTags($id, $oldTags, $data['Course']['tags']);
                    $this->updateChapters($id, $oldChapters, $data['Chapter']);

                    $this->redirect(array('action' => 'admin_detail', $id));
                }, $this->Course->saveAll($this->request->data['Course'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            
            $this->set('item', $item);
        });
    }

    public function admin_add($id = null) {
        $this->getTags();
        $this->set('title', __('Courses'));
        $this->set('small_title', __('Add Course'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Courses'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Course'), 'slug' => '#'));
        if ($this->request->is('post')) {
            list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
            if ($thumb) {
                $this->request->data['Course']['thumb'] = $thumb;
            }
            $this->ExtendControl->if_crud_complete_then(function() {
                $course_id = $this->Course->getLastInsertID();
                $data = $this->request->data;
                // Insert tags and chapters
                $this->updateTags($course_id, [], $data['Course']['tags']);
                $this->updateChapters($course_id, [], $data['Chapter']);
                $this->redirect(array('action' => 'admin_detail', $course_id));
            }, $this->Course->saveAll($this->request->data['Course'], array('deep' => true)));
        }
    }
    
    private function getCoursesByTag($tag_id){
        $result = [];
        $items = $this->CourseTag->find('all', [
            'contain' => false,
            'conditions' => [
                'tag_id' => $tag_id
            ]
        ]);
        if(!empty($items)){
            foreach($items as $item){
                $result[] = $item['CourseTag']['course_id'];
            }
        }
        return $result;
    }

    public function admin_ajax_add_chapter() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    
    private function updateTags($course_id, $old_tags, $new_tags){
        if(!empty($new_tags)){
            $arrTags = [];
            foreach($new_tags as $tag_id){
                if(!in_array($tag_id, $old_tags)){
                    $arrTags[] = ['CourseTag' => [
                            'course_id' => $course_id,
                            'tag_id' => $tag_id
                        ]
                    ];
                }
            }
            if(!empty($arrTags)){
                $this->CourseTag->saveAll($arrTags);
            }
        }

        // Remove old tags
        if(!empty($old_tags)){
            foreach ($old_tags as $tag_id) {
                if(!in_array($tag_id, $new_tags)){
                    $this->CourseTag->deleteAll(['tag_id' => $tag_id, 'course_id' => $course_id]);
                }
            }
        }
        
        return true;
    }
    
    private function updateChapters($course_id, $old_chapters, $new_chapters){
        $chapter_titles = $new_chapters['title'];
        $chapter_ids = isset($new_chapters['id']) ? $new_chapters['id'] : [];
        if(!empty($chapter_titles)){
            $arrChapters = [];
            foreach($chapter_titles as $chap_id => $chap_title){
                if(!isset($chapter_ids[$chap_id])){ // insert new
                    $arrChapters[] = ['Chapter' => [
                            'course_id' => $course_id,
                            'title' => $chap_title,
                            'sort_order' => $new_chapters['sort_order'][$chap_id]
                        ]
                    ];
                }
            }
            if(!empty($arrChapters)){
                $this->Chapter->saveAll($arrChapters);
            }
        }
        
        // remove or update
        if(!empty($old_chapters)){
            foreach($old_chapters as $chap_id => $chap_title){
                if(!isset($chapter_ids[$chap_id])){
                    $this->Chapter->delete($chap_id);
                } else { // update title, sort order
                    $this->Chapter->id = $chap_id;
                    $chap = [
                        'title' => $chapter_titles[$chap_id],
                        'sort_order' => $new_chapters['sort_order'][$chap_id]
                    ];
                    $this->Chapter->save($chap);
                }
            }
        }
        
        return true;
    }
}
