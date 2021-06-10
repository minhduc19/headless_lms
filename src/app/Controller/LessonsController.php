<?php

App::uses('AppController', 'Controller');

class LessonsController extends AppController {

    public $uses = array('Lesson', 'LessonTag', 'Unit', 'Course', 'TeacherLesson', 
        'Teacher', 'TeacherSkill', 'TeacherLessonSkill');
    public $components = array('CRUD', 'ExtendControl', 'Filter', 'S3');
    
    public $paginate = array(
        'limit' => 10,
        'maxLimit' => 500
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function isAuthorized($user) {
        $teacher_actions = [
            'teacher_index',
            'teacher_add',
            'teacher_edit',
            'teacher_detail',
            'teacher_delete',
        ];
        if ($user['type'] == 'admin') {
            return true;
        } else if($user['type'] == 'teacher' && in_array($this->action, $teacher_actions)){
            return true;
        }
        return false;
    }
    
    private function getTeacherSkills(){
        $skills = $this->TeacherSkill->find('all', [
            'conditions' => ['teacher_id' => $this->auth_user['id']]
        ]);
        $arr_skills = [];
        if(!empty($skills)){
            foreach($skills as $skill){
                $arr_skills[$skill['TeacherSkill']['id']] = $skill['TeacherSkill']['name'];
            }
        }
        $this->set('skills', $arr_skills);
        return $arr_skills;
    }
    public function teacher_index()
    {
        $this->getTeacherSkills();
        $this->set('title_for_layout', __('List Lessons'));
        $this->set('title', __('Lessons management'));
        $this->set('small_title', __('Lessons'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List lessons'), 'slug' => '#'));
        $conditions = [
            'TeacherLesson.teacher_id' => $this->auth_user['id']
        ];
        $keyword = '';
        $skill_id = '';
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
                    $conditions += array('TeacherLesson.title LIKE ' => '%' . $data['keyword'] . '%');
                }
                $keyword = $data['keyword'];
            }

            if (isset($data['skill_id'])) {
                if($data['skill_id'] != ''){
                    $skill_id = $data['skill_id'];
                    $teacher_lesson_skills = $this->TeacherLessonSkill->find('all', [
                        'fields' => ['teacher_lesson_id'],
                        'conditions' => ['teacher_skill_id' => $skill_id]
                    ]);
                    $lesson_ids = [];
                    if(!empty($teacher_lesson_skills)){
                        foreach($teacher_lesson_skills as $val){
                            $lesson_ids[] = $val['TeacherLessonSkill']['teacher_lesson_id'];
                        }
                    }
                    $conditions += array('TeacherLesson.id' => $lesson_ids);
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
        //var_dump($conditions); die();
        $this->Paginator->settings = $this->paginate;
        $items = $this->Paginator->paginate('TeacherLesson', $conditions);
        $this->set('keyword', $keyword);
        $this->set('skill_id', $skill_id);
        $this->set('flag', $flag);
        $this->set('limit_page', $limit_page);
        $this->set('items', $items);
        $this->set('query', array(
            'keyword' => $keyword,
            'skill_id' => $skill_id,
            'flag' => $flag,
            'limit_page' => $limit_page,
        ));
    }
    
    public function teacher_detail($id) {
        $this->getTeacherSkills();
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Lesson\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lesson\'s detail'), 'slug' => '#'));
        
        $item = $this->TeacherLesson->find('first', [
            'conditions' => [
                'TeacherLesson.id' => $id,
                'TeacherLesson.teacher_id' => $this->auth_user['id']
            ],
            'contain' => ['TeacherScene' => [
                'order' => [
                    'TeacherScene.sort_order' => 'DESC',
                    'TeacherScene.id' => 'ASC'
                ]
            ]]
        ]);
        
        if(empty($item)){
            $this->redirect(['action' => 'index', 'teacher' => true]);
        }
        
        $this->set('item', $item);
        $this->request->data = $item;
        $this->request->data['_method'] = 'GET';
        
        $query = $this->request->query;
        $active = 'info';
        if(isset($query['active']) && in_array($query['active'], ['info', 'scenes'])){
            $active = $query['active'];
        }
        $this->set('active', $active);
    }

    public function teacher_delete($id = null) {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->TeacherLesson, true);
        $this->updateTotalLessons($this->auth_user['id']);
    }

    public function teacher_delete_checked() {
        $this->CRUD->delete_checked($this->TeacherLesson, true, 'index');
        $this->updateTotalLessons($this->auth_user['id']);
    }

    public function teacher_edit($id = null) {
        $this->getTeacherSkills();
        $this->set('title', __('Lessons'));
        $this->set('small_title', __('Edit Lesson'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Lesson'), 'slug' => '#'));
        $this->CRUD->detail_of($this->TeacherLesson, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
                if ($thumb) {
                    $this->request->data['TeacherLesson']['thumb'] = $thumb;
                }
                $skills = $this->request->data['TeacherLesson']['skills'];
                $this->updateLessonSkills($id, $skills);
                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    $this->redirect(array('action' => 'teacher_detail', $id));
                }, $this->TeacherLesson->saveAll($this->request->data['TeacherLesson'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            
            $this->set('item', $item);
        });
    }

    public function teacher_add($id = null) {
        $this->getTeacherSkills();
        $this->set('title', __('Lessons'));
        $this->set('small_title', __('Add Lesson'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Lesson'), 'slug' => '#'));
        if ($this->request->is('post')) {
            //pr($this->request->data); die();
            list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
            if ($thumb) {
                $this->request->data['TeacherLesson']['thumb'] = $thumb;
            }
            $this->request->data['TeacherLesson']['teacher_id'] = $this->auth_user['id'];
            $this->ExtendControl->if_crud_complete_then(function() {
                // Update total lessons of teacher
                $id = $this->TeacherLesson->getLastInsertID();
                $this->updateTotalLessons($this->auth_user['id']);
                $this->updateLessonSkills($id, $this->request->data['TeacherLesson']['skills']);
                $this->redirect(array('controller' => 'scenes', 'action' => 'teacher_add', '?' => ['lesson_id' => $id]));
            }, $this->TeacherLesson->saveAll($this->request->data['TeacherLesson'], array('deep' => true)));
        }
    }

    public function admin_index()
    {
        $this->getTags();
        $this->getCourses();
        $this->set('title_for_layout', __('List Lessons'));
        $this->set('title', __('Lessons management'));
        $this->set('small_title', __('Lessons'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List lessons'), 'slug' => '#'));
        $conditions = array();
        $keyword = '';
        $tag_id = '';
        $course_id = '';
        $chapter_id = '';
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
                    $conditions += array('Lesson.title LIKE ' => '%' . $data['keyword'] . '%');
                }
                $keyword = $data['keyword'];
            }

            if (isset($data['tag_id'])) {
                if($data['tag_id'] != ''){
                    $ids = $this->getLessonsByTag($data['tag_id']);
                    $conditions += ['Lesson.id' => $ids];
                    $tag_id = $data['tag_id'];
                }
            }
            if (isset($data['course_id']) && $data['course_id'] != '') {
                $conditions += ['Lesson.course_id' => $data['course_id']];
                $course_id = $data['course_id'];
            }
            if (isset($data['chapter_id']) && $data['chapter_id'] != '') {
                $conditions += ['Lesson.chapter_id' => $data['chapter_id']];
                $chapter_id = $data['chapter_id'];
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

        $this->getChapters($course_id);
        $this->Paginator->settings = $this->paginate;
        $items = $this->Paginator->paginate('Lesson', $conditions);
        $this->set('keyword', $keyword);
        $this->set('tag_id', $tag_id);
        $this->set('course_id', $course_id);
        $this->set('chapter_id', $chapter_id);
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
        $this->set('small_title', __('Lesson\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lesson\'s detail'), 'slug' => '#'));
        
        $item = $this->Lesson->find('first', [
            'contain' => [
                'Unit' => ['order' => ['Unit.sort_order' => 'DESC']],
                'Chapter', 'Course', 'LessonTag' => 'Tag'],
            'conditions' => [
                'Lesson.id' => $id
            ]
        ]);
        
        if(empty($item)){
            $this->redirect(['action' => 'index', 'admin' => true]);
        }
        
        $this->set('item', $item);
        $this->request->data = $item;
        $this->request->data['_method'] = 'GET';
        
        $tags = [];
        if(!empty($item['LessonTag'])){
            foreach($item['LessonTag'] as $tag){
                if(!empty($tag['Tag'])){
                    $tags[] = '#' . $tag['Tag']['name'];
                }
            }
        }
        $tags = implode(', ', $tags);
        $this->set('tags', $tags);
    }

    public function admin_delete($id = null) {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Lesson, true);
    }

    public function admin_delete_checked() {
        $this->CRUD->delete_checked($this->Lesson, true, 'index');
    }

    public function admin_edit($id = null) {
        $this->getTags();
        $this->getCourses();
        $this->set('title', __('Lessons'));
        $this->set('small_title', __('Edit Lesson'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Lesson'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Lesson, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
                if ($thumb) {
                    $this->request->data['Lesson']['thumb'] = $thumb;
                }

                $this->request->data['Lesson']['total_unit'] = isset($this->request->data['Unit']['title']) ? count($this->request->data['Unit']['title']) : 0;
                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    Cache::clearGroup('new_releases', 'short');
                    $data = $this->request->data;
                    // Update tags
                    $oldTags = [];
                    $oldUnits = [];
                    if(!empty($item['LessonTag'])){
                        foreach($item['LessonTag'] as $tag){
                            $oldTags[] = $tag['tag_id'];
                        }
                    }
                    
                    if(!empty($item['Unit'])){
                        foreach($item['Unit'] as $unit){
                            $oldUnits[$unit['id']] = $unit;
                        }
                    }
                    $this->updateTags($id, $oldTags, $data['Lesson']['tags']);
                    $this->updateUnits($id, $oldUnits, $data['Unit']);
                    $this->updateCourse($this->request->data['Lesson']['course_id']);
                    
                    $this->redirect(array('action' => 'admin_detail', $id));
                }, $this->Lesson->saveAll($this->request->data['Lesson'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            
            $this->getChapters($item['Lesson']['course_id']);
            $this->set('item', $item);
        });
    }

    public function admin_add($id = null) {
        $this->getTags();
        $this->getCourses();
        $this->set('title', __('Lessons'));
        $this->set('small_title', __('Add Lesson'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Lessons'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Lesson'), 'slug' => '#'));
        if ($this->request->is('post')) {
            list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
            if ($thumb) {
                $this->request->data['Lesson']['thumb'] = $thumb;
            }
            $this->request->data['Lesson']['total_unit'] = isset($this->request->data['Unit']['title']) ? count($this->request->data['Unit']['title']) : 0;
            $this->ExtendControl->if_crud_complete_then(function() {
                Cache::clearGroup('new_releases', 'short');
                $lesson_id = $this->Lesson->getLastInsertID();
                $data = $this->request->data;
                // Insert tags + units
                $this->updateTags($lesson_id, [], $data['Lesson']['tags']);
                $this->updateUnits($lesson_id, [], $data['Unit']);
                $this->updateCourse($this->request->data['Lesson']['course_id']);
                $this->redirect(array('action' => 'admin_detail', $lesson_id));
            }, $this->Lesson->saveAll($this->request->data['Lesson'], array('deep' => true)));
        }
    }
    
    private function getLessonsByTag($tag_id){
        $result = [];
        $items = $this->LessonTag->find('all', [
            'contain' => false,
            'conditions' => [
                'tag_id' => $tag_id
            ]
        ]);
        if(!empty($items)){
            foreach($items as $item){
                $result[] = $item['LessonTag']['lesson_id'];
            }
        }
        return $result;
    }

    public function admin_ajax_add_unit() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    
    public function admin_ajax_get_chapters($id = null){
        $this->autoRender = false;
        $this->response->type('json');
        $response = $this->getChapters($id);
        return $this->response->body(json_encode($response));
    }
    
    private function updateUnits($lesson_id, $old_units, $new_units){
        if(!empty($new_units['title'])){
            $arrUnits = [];
            foreach($new_units['title'] as $key => $title){
                list($s3_key, $thumb) = $this->S3->upload($new_units['thumb'][$key], array('type' => 'thumb'));
                list($url, $zip_url) = $this->Upload->upload_unit($lesson_id, $new_units['html_file'][$key]);
                
                if(isset($old_units[$key])){ // Update Unit
                    $unit = $old_units[$key];
                    
                    if($thumb){
                        $unit['thumb'] = $thumb;
                    }
                    if($url){
                        $unit['url'] = $url;
                    }
                    if($zip_url){
                        $unit['zip_url'] = $zip_url;
                    }
                    $unit['title'] = $title;
                    $unit['short_description'] = $new_units['short_description'][$key];
                    $unit['sort_order'] = $new_units['sort_order'][$key];
                    $unit['type'] = $new_units['type'][$key];
                    $this->Unit->id = $unit['id'];
                    $this->Unit->save($unit);
                } else { // Insert unit
                    $arrUnits[] = ['Unit' => [
                            'type' => $new_units['type'][$key],
                            'lesson_id' => $lesson_id,
                            'title' => $title,
                            'short_description' => $new_units['short_description'][$key],
                            'sort_order' => $new_units['sort_order'][$key],
                            'thumb' => $thumb,
                            'url' => $url ? $url : null,
                            'zip_url' => $zip_url ? $zip_url : null
                        ]
                    ];
                }
            }
            if(!empty($arrUnits)){
                $this->Unit->saveAll($arrUnits);
            }
        }
        
        // remove old units
        if(!empty($old_units)){
            foreach ($old_units as $unit_id => $unit) {
                if(!isset($new_units['title'][$unit_id])){
                    $this->Unit->delete($unit_id);
                }
            }
        }
    }
    
    private function updateTags($lesson_id, $old_tags, $new_tags){
        if(!empty($new_tags)){
            $arrTags = [];
            foreach($new_tags as $tag_id){
                if($tag_id != '' && !in_array($tag_id, $old_tags)){
                    $arrTags[] = ['LessonTag' => [
                            'lesson_id' => $lesson_id,
                            'tag_id' => $tag_id
                        ]
                    ];
                }
            }
            if(!empty($arrTags)){
                $this->LessonTag->saveAll($arrTags);
            }
        }

        // Remove old tags
        if(!empty($old_tags)){
            if(!empty($old_tags)){
                foreach ($old_tags as $tag_id) {
                    if(!in_array($tag_id, $new_tags)){
                        $this->LessonTag->deleteAll(['tag_id' => $tag_id, 'lesson_id' => $lesson_id]);
                    }
                }
            }
        }
        return true;
    }
    
    private function updateCourse($course_id){
        $count = $this->Lesson->find('count', [
            'contain' => false,
            'conditions' => [
                'Lesson.course_id' => $course_id
            ]
        ]);
        $this->Course->id = $course_id;
        $this->Course->saveField('total_lesson', $count);
        return true;
    }
    
    private function updateTotalLessons($teacher_id){
        $count = $this->TeacherLesson->find('count', [
           'teacher_id' => $teacher_id 
        ]);
        $this->Teacher->id = $teacher_id;
        $this->Teacher->saveField('total_lesson', $count);
    }
    
    private function updateLessonSkills($lesson_id, $skills){
        $old_skills = $this->TeacherLessonSkill->find('all', [
            'conditions' => ['teacher_lesson_id' => $lesson_id]
        ]);
        $old_ids = [];
        if(!empty($old_skills)){
            foreach($old_skills as $skill){
                $old_ids[] = $skill['TeacherLessonSkill']['teacher_skill_id'];
                if(!in_array($skill['TeacherLessonSkill']['teacher_skill_id'], $skills)){
                    // Remove old items
                    $this->TeacherLessonSkill->delete($skill['TeacherLessonSkill']['id']);
                }
            }
        }
        $arrData = [];
        if(!empty($skills)){
            foreach($skills as $skill_id){
                if($skill_id != '' && !in_array($skill_id, $old_ids)){
                    $arrData[] = ['TeacherLessonSkill' => [
                            'teacher_lesson_id' => $lesson_id,
                            'teacher_skill_id' => $skill_id
                            ]
                    ];
                }
            }
        }
        
        if(!empty($arrData)){
            $this->TeacherLessonSkill->saveAll($arrData);
        }
        
        // sync skill lesson_count
        if(!empty($skills)){
            foreach($skills as $skill_id){
                $count = $this->TeacherLessonSkill->find('count', [
                    'conditions' => ['teacher_skill_id' => $skill_id]
                ]);
                $this->TeacherSkill->id = $skill_id;
                $this->TeacherSkill->saveField('lesson_count', $count);
            }
        }
        return true;
        
    }
}
