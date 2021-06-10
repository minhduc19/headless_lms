<?php

App::uses('AppController', 'Controller');

class ScenesController extends AppController {

    public $uses = array('Unit', 'Scene', 'Media', 'Feedback', 'TeacherScene', 'TeacherLesson');
    public $components = array('CRUD', 'ExtendControl', 'Filter', 'S3');
    
    public $paginate = array(
        'limit' => 50,
        'maxLimit' => 500
    );

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function isAuthorized($user) {
        $teacher_actions = [
            'teacher_index',
            'teacher_add',
            'teacher_delete',
            'teacher_edit',
            'teacher_detail',
            'teacher_ajax_update_order',
            'teacher_ajax_add_feedback',
            'teacher_ajax_add_media',
            'teacher_ajax_get_lessons',
        ];
        if ($user['type'] == 'admin') {
            return true;
        } else if($user['type'] == 'teacher' && in_array($this->action, $teacher_actions)){
            return true;
        }
        return false;
    }

    public function admin_index()
    {
        $this->getCourses();
        $this->getLessons();
        $this->getUnits();
        $this->set('title_for_layout', __('List Scenes'));
        $this->set('title', __('Scenes management'));
        $this->set('small_title', __('Scenes'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List Scenes'), 'slug' => '#'));
        $conditions = array();
        $keyword = '';
        $course_id = '';
        $lesson_id = '';
        $unit_id = '';
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
                    $conditions += array('Scene.title LIKE ' => '%' . $data['keyword'] . '%');
                }
                $keyword = $data['keyword'];
            }
            if (isset($data['unit_id']) && $data['unit_id'] != '') {
                $conditions += ['Scene.unit_id' => $data['unit_id']];
                $unit_id = $data['unit_id'];
            } else if (isset($data['lesson_id']) && $data['lesson_id'] != '') {
                $units = $this->getUnits($data['lesson_id']);
                $conditions += ['Scene.unit_id' => array_keys($units)];
            } else if (isset($data['course_id']) && $data['course_id'] != '') { // lesson_id not set
                $lessons = $this->getLessons($data['course_id']);
                $units = $this->getUnits(array_keys($lessons));
                $conditions += ['Scene.unit_id' => array_keys($units)];
            }
            
            if (isset($data['course_id']) && $data['course_id'] != '') {
                $course_id = $data['course_id'];
                $lessons = $this->getLessons($course_id);
                $units = $this->getUnits(array_keys($lessons));
            }
            
            if (isset($data['lesson_id']) && $data['lesson_id'] != '') {
                $lesson_id = $data['lesson_id'];
                $this->getUnits($lesson_id);
            }
            
            if (isset($data['unit_id']) && $data['unit_id'] != '' && $lesson_id == "") {
                $unit = $this->Unit->findById($data['unit_id']);
                $lesson_id = $unit['Lesson']['id'];
                $course_id = $unit['Lesson']['course_id'];
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
        
        $this->paginate['contain'] = ['Unit'];
        $this->Paginator->settings = $this->paginate;
        $items = $this->Paginator->paginate('Scene', $conditions);
        $this->set('keyword', $keyword);
        $this->set('course_id', $course_id);
        $this->set('lesson_id', $lesson_id);
        $this->set('unit_id', $unit_id);
        $this->set('flag', $flag);
        $this->set('limit_page', $limit_page);
        $this->set('items', $items);
    }

    public function admin_delete($id = null) {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Scene, true);
    }

    public function admin_delete_checked() {
        $this->CRUD->delete_checked($this->Scene, true, 'index');
    }
    
    public function admin_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Scene\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scenes'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Scene, $id, function($item) {
            $this->set('item', $item);
            $lesson = $this->Lesson->findById($item['Unit']['lesson_id']);
            if(!empty($lesson)){
                $this->set('lesson', $lesson['Lesson']);
                $this->set('course', $lesson['Course']);
            }
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function admin_add($id = null)
    {
        $this->getLastestGroup();
        $this->getCourses();
        $this->set('title', __('Scene'));
        $this->set('small_title', __('Add Scene'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scenes'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Scene'), 'slug' => '#'));
        if ($this->request->is('post')) {
            list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
            if ($thumb) {
                $this->request->data['Scene']['thumb'] = $thumb;
            }
            $this->ExtendControl->if_crud_complete_then(function() {
                $data = $this->request->data;
                $id = $this->Scene->getLastInsertID();
                $this->updateFeedback($id, [], $data['Feedback']);
                $this->updateMedias($id, [], $data['Media']);
                $this->redirect(array('action' => 'admin_edit', $id));
            }, $this->Scene->saveAll($this->request->data['Scene'], array('deep' => false)));
        }
    }
    
    public function admin_edit($id = null)
    {
        $this->getCourses();
        $this->set('title', __('Scene'));
        $this->set('small_title', __('Edit Scene'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scene'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Scene'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Scene, $id, function($item) use ($id) {
            $unit = $this->Unit->findById($item['Scene']['unit_id']);
            if($unit){
                $this->getUnits($unit['Unit']['lesson_id']);
                $lesson = $this->Lesson->findById($unit['Unit']['lesson_id']);
                if($lesson){
                    $this->getLessons($lesson['Lesson']['course_id']);
                } else {
                    $this->set('lessons', []);
                }
            }
            if ($this->request->is('put')) {
                list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
                if($thumb){
                    $this->request->data['Scene']['thumb'] = $thumb;
                }
                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    $data = $this->request->data;
                    $oldMedias = [];
                    $oldFeedbacks = [];
                    if(!empty($item['Media'])){
                        foreach($item['Media'] as $media){
                            $oldMedias[$media['id']] = $media;
                        }
                    }
                    if(!empty($item['Feedback'])){
                        foreach($item['Feedback'] as $feedback){
                            $oldFeedbacks[$feedback['id']] = $feedback;
                        }
                    }
                    
                    $this->updateFeedback($id, $oldFeedbacks, $data['Feedback']);
                    $this->updateMedias($id, $oldMedias, $data['Media']);
                    $this->redirect(array('action' => 'admin_index'));
                }, $this->Scene->saveAll($this->request->data['Scene'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }
    
    public function admin_ajax_get_lessons($id = null){
        $this->autoRender = false;
        $this->response->type('json');
        $response = $this->getLessons($id);
        return $this->response->body(json_encode($response));
    }
    
    public function admin_ajax_get_units($id = null){
        $this->autoRender = false;
        $this->response->type('json');
        $response = $this->getUnits($id);
        return $this->response->body(json_encode($response));
    }
    public function admin_ajax_add_media() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    public function admin_ajax_add_feedback() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    
    public function admin_ajax_update_order(){
        $this->layout = false;
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $data = $this->request->data;
        if(!isset($data['sort_order']) || !isset($data['id'])){
            return false;
        }
        $this->Scene->id = $data['id'];
        $this->Scene->saveField('sort_order', $data['sort_order']);
        return true;
    }
    
    
    private function updateMedias($scene_id, $old_medias, $new_medias){
        if(!empty($new_medias['type'])){
            $arrMedias = [];
            foreach($new_medias['type'] as $key => $type){
                if($type == ""){
                    $type = $this->_getFileType($new_medias['content'][$key]);
                    if($type == false){
                        continue;
                    }
                }
                list($cover_key, $cover) = $this->S3->upload($new_medias['cover'][$key], array('type' => 'cover'));
                list($s3_key, $url) = $this->S3->upload($new_medias['content'][$key], array('type' => 'media'));
                if(isset($old_medias[$key])){ // Update Media
                    $media = $old_medias[$key];
                    if($url){
                        $media['url'] = $url;
                        $media['s3_key'] = $s3_key;
                    }
                    $media['type'] = $type;
                    $media['sort_order'] = $new_medias['sort_order'][$key];
                    if($cover){
                        $media['cover'] = $cover;
                    }
                    $this->Media->id = $media['id'];
                    $this->Media->save($media);
                } else { // Insert media
                    if($url){
                        $arrMedias[] = ['Media' => [
                                'scene_id' => $scene_id,
                                'type' => $type,
                                'sort_order' => $new_medias['sort_order'][$key],
                                'url' => $url,
                                's3_key' => $s3_key,
                                'cover' => $cover ? $cover : ""
                            ]
                        ];
                    }
                }
            }
            if(!empty($arrMedias)){
                $this->Media->saveAll($arrMedias);
            }
        }
        
        // remove old medias
        if(!empty($old_medias)){
            foreach ($old_medias as $media_id => $media) {
                if(!isset($new_medias['type'][$media_id])){
                    $item = $this->Media->findById($media_id);
                    if($item){
                        $this->Media->delete($media_id);
                        // Delete S3
                        if($item['Media']['s3_key'] != ''){
                            $this->S3->delete($item['Media']['s3_key']);
                        }
                    }
                }
            }
        }
    }
    
    private function updateFeedback($scene_id, $old_items, $new_items){
        if(!empty($new_items['type'])){
            $arrItems = [];
            foreach($new_items['type'] as $key => $type){
                if(isset($old_items[$key])){ // Update Media
                    $item = $old_items[$key];
                    $item['type'] = $type;
                    $item['answer'] = $new_items['answer'][$key];
                    $item['feedback'] = $new_items['feedback'][$key];
                    $this->Feedback->id = $item['id'];
                    $this->Feedback->save($item);
                } else { // Insert feedback
                    if($new_items['feedback'][$key] != ""){
                        $arrItems[] = ['Feedback' => [
                                'scene_id' => $scene_id,
                                'type' => $type,
                                'answer' => $new_items['answer'][$key],
                                'feedback' => $new_items['feedback'][$key],
                            ]
                        ];
                    }
                }
            }
            if(!empty($arrItems)){
                $this->Feedback->saveAll($arrItems);
            }
        }
        
        // remove old items
        if(!empty($old_items)){
            foreach ($old_items as $id => $item) {
                if(!isset($new_items['type'][$id])){
                    $this->Feedback->delete($id);
                }
            }
        }
        return true;
    }
    
    private function getLastestGroup(){
        $item = $this->Scene->find('first',[
            'contain' => false,
            'fields' => ['group_code'],
            'conditions' => [
                'group_code != ' => ''
            ],
            'order' => ['modified' => 'DESC']
        ]);
        
        if(!empty($item)){
            $this->set('lastest_code', $item['Scene']['group_code']);
            return $item['Scene']['group_code'];
        }
        return false;
    }
    
    private function _getFileType($file){
        $type = mime_content_type($file['tmp_name']);
        $images = [
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/svg+xml'
        ];
        $videos = [
            'video/quicktime',
            'video/mp4'
        ];
        $audios = [
            'audio/mpeg'
        ];
        
        if(in_array($type, $images)){
            return 'image';
        } else if (in_array($type, $videos)){
            return 'video';
        } else if (in_array($type, $audios)){
            return 'audio';
        }
        return false;
    }
    
    
    
    public function teacher_index()
    {
        $this->getTeacherLessons();
        $this->set('title_for_layout', __('List Scenes'));
        $this->set('title', __('Scenes management'));
        $this->set('small_title', __('Scenes'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List Scenes'), 'slug' => '#'));
        $conditions = ['TeacherScene.teacher_id' => $this->auth_user['id']];
        $keyword = '';
        $lesson_id = '';
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
                    $conditions += array('TeacherScene.title LIKE ' => '%' . $data['keyword'] . '%');
                }
                $keyword = $data['keyword'];
            }
            if (isset($data['lesson_id']) && $data['lesson_id'] != '') {
                $lesson_id = $data['lesson_id'];
                $conditions += ['TeacherScene.teacher_lesson_id' => $data['lesson_id']];
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
            $this->paginate['order'] = ['TeacherScene.sort_order' => 'DESC', 'TeacherScene.id' => 'DESC'];
        }
        
        $this->paginate['contain'] = ['TeacherLesson'];
        $this->Paginator->settings = $this->paginate;
        $items = $this->Paginator->paginate('TeacherScene', $conditions);
        $this->set('keyword', $keyword);
        $this->set('lesson_id', $lesson_id);
        $this->set('flag', $flag);
        $this->set('limit_page', $limit_page);
        $this->set('items', $items);
    }

    public function teacher_delete($id = null) {
        $redirect = '/teacher/scenes';
        $query = $this->request->query;
        if(isset($query['redirect'])){
            $redirect = $query['redirect'];
        }
        $this->set('redirect', $redirect);
        $this->CRUD->ajax_delete_individual_by_id($id, $this->TeacherScene, true);
    }

    public function teacher_delete_checked() {
        $this->CRUD->delete_checked($this->TeacherScene, true, 'teacher_index');
    }
    
    public function teacher_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Scene\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scenes'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->TeacherScene, $id, function($item) {
            $this->set('item', $item);
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function teacher_add($id = null)
    {
        $this->teacherGetLastestGroup();
        $this->getTeacherLessons();
        $this->set('title', __('Scene'));
        $this->set('small_title', __('Add Scene'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scenes'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Scene'), 'slug' => '#'));
        
        $query = $this->request->query;
        $lesson_id = null;
        if(isset($query['lesson_id'])){
            $lesson_id = $query['lesson_id'];
        }
        $this->set('lesson_id', $lesson_id);
                
        if ($this->request->is('post')) {
            list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
            if ($thumb) {
                $this->request->data['TeacherScene']['thumb'] = $thumb;
            }
            $this->request->data['TeacherScene']['teacher_id'] = $this->auth_user['id'];
            
            $this->ExtendControl->if_crud_complete_then(function() {
                $data = $this->request->data;
                $id = $this->TeacherScene->getLastInsertID();
                $this->teacherUpdateFeedback($id, [], $data['Feedback']);
                $this->teacherUpdateMedias($id, [], $data['Media']);
                
                //$this->redirect(array('action' => 'teacher_add', '?' => ['lesson_id' => $this->request->data['TeacherScene']['teacher_lesson_id']]));
                $this->redirect(array('controller' => 'lessons', 'action' => 'teacher_detail', $this->request->data['TeacherScene']['teacher_lesson_id'], '?' => ['active' => 'scenes']));
            }, $this->TeacherScene->saveAll($this->request->data['TeacherScene'], array('deep' => false)));
        }
    }
    
    public function teacher_edit($id = null)
    {
        $this->getTeacherLessons();
        $this->set('title', __('Scene'));
        $this->set('small_title', __('Edit Scene'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Scene'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Scene'), 'slug' => '#'));
        $this->CRUD->detail_of($this->TeacherScene, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                list($s3_key, $thumb) = $this->S3->upload($this->request->data['Image']['thumb'], array('type' => 'thumb'));
                if($thumb){
                    $this->request->data['TeacherScene']['thumb'] = $thumb;
                }
                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    $data = $this->request->data;
                    $oldMedias = [];
                    $oldFeedbacks = [];
                    if(!empty($item['Media'])){
                        foreach($item['Media'] as $media){
                            $oldMedias[$media['id']] = $media;
                        }
                    }
                    if(!empty($item['Feedback'])){
                        foreach($item['Feedback'] as $feedback){
                            $oldFeedbacks[$feedback['id']] = $feedback;
                        }
                    }
                    
                    $this->teacherUpdateFeedback($id, $oldFeedbacks, $data['Feedback']);
                    $this->teacherUpdateMedias($id, $oldMedias, $data['Media']);
                    $this->redirect(array('action' => 'teacher_index'));
                }, $this->TeacherScene->saveAll($this->request->data['TeacherScene'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }
    
    public function teacher_ajax_get_lessons($id = null){
        $this->autoRender = false;
        $this->response->type('json');
        $response = $this->getTeacherLessons();
        return $this->response->body(json_encode($response));
    }
    
    public function teacher_ajax_add_media() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    public function teacher_ajax_add_feedback() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
    
    public function teacher_ajax_update_order(){
        $this->layout = false;
        $this->autoRender = false;
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $data = $this->request->data;
        if(!isset($data['sort_order']) || !isset($data['id'])){
            return false;
        }
        $this->TeacherScene->id = $data['id'];
        $this->TeacherScene->saveField('sort_order', $data['sort_order']);
        return true;
    }
    
    
    private function teacherUpdateMedias($scene_id, $old_medias, $new_medias){
        if(!empty($new_medias['type'])){
            $arrMedias = [];
            foreach($new_medias['type'] as $key => $type){
                if($type == ""){
                    $type = $this->_getFileType($new_medias['content'][$key]);
                    if($type == false){
                        continue;
                    }
                }
                list($cover_key, $cover) = $this->S3->upload($new_medias['cover'][$key], array('type' => 'cover'));
                list($s3_key, $url) = $this->S3->upload($new_medias['content'][$key], array('type' => 'media'));
                if(isset($old_medias[$key])){ // Update Media
                    $media = $old_medias[$key];
                    if($url){
                        $media['url'] = $url;
                        $media['s3_key'] = $s3_key;
                    }
                    $media['type'] = $type;
                    $media['sort_order'] = $new_medias['sort_order'][$key];
                    if($cover){
                        $media['cover'] = $cover;
                    }
                    $this->Media->id = $media['id'];
                    $this->Media->save($media);
                } else { // Insert media
                    if($url){
                        $arrMedias[] = ['Media' => [
                                'teacher_scene_id' => $scene_id,
                                'type' => $type,
                                'sort_order' => $new_medias['sort_order'][$key],
                                'url' => $url,
                                's3_key' => $s3_key,
                                'cover' => $cover ? $cover : ""
                            ]
                        ];
                    }
                }
            }
            if(!empty($arrMedias)){
                $this->Media->saveAll($arrMedias);
            }
        }
        
        // remove old medias
        if(!empty($old_medias)){
            foreach ($old_medias as $media_id => $media) {
                if(!isset($new_medias['type'][$media_id])){
                    $item = $this->Media->findById($media_id);
                    if($item){
                        $this->Media->delete($media_id);
                        // Delete S3
                        if($item['Media']['s3_key'] != ''){
                            $this->S3->delete($item['Media']['s3_key']);
                        }
                    }
                }
            }
        }
    }
    
    private function teacherUpdateFeedback($scene_id, $old_items, $new_items){
        if(!empty($new_items['type'])){
            $arrItems = [];
            foreach($new_items['type'] as $key => $type){
                if(isset($old_items[$key])){ // Update Media
                    $item = $old_items[$key];
                    $item['type'] = $type;
                    $item['answer'] = $new_items['answer'][$key];
                    $item['feedback'] = $new_items['feedback'][$key];
                    $this->Feedback->id = $item['id'];
                    $this->Feedback->save($item);
                } else { // Insert feedback
                    if($new_items['feedback'][$key] != ""){
                        $arrItems[] = ['Feedback' => [
                                'teacher_scene_id' => $scene_id,
                                'type' => $type,
                                'answer' => $new_items['answer'][$key],
                                'feedback' => $new_items['feedback'][$key],
                            ]
                        ];
                    }
                }
            }
            if(!empty($arrItems)){
                $this->Feedback->saveAll($arrItems);
            }
        }
        
        // remove old items
        if(!empty($old_items)){
            foreach ($old_items as $id => $item) {
                if(!isset($new_items['type'][$id])){
                    $this->Feedback->delete($id);
                }
            }
        }
        return true;
    }
    
    private function teacherGetLastestGroup(){
        $item = $this->TeacherScene->find('first',[
            'contain' => false,
            'fields' => ['group_code'],
            'conditions' => [
                'group_code != ' => ''
            ],
            'order' => ['modified' => 'DESC']
        ]);
        if(!empty($item)){
            $this->set('lastest_code', $item['TeacherScene']['group_code']);
            return $item['TeacherScene']['group_code'];
        }
        return false;
    }
    
    
    protected function getTeacherLessons($lesson_id = null){
        $conditions = [
            'TeacherLesson.teacher_id' => $this->auth_user['id']
        ];
        if($lesson_id != null){
            $conditions = [
                'TeacherLesson.id' => $lesson_id
            ];
        }
        $items = $this->TeacherLesson->find('all', [
            'contain' => false,
            'conditions' => $conditions,
            //'order' => ['TeacherLesson.sort_order' => 'DESC']
            ]);
        $lessons = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $lessons[$item['TeacherLesson']['id']] = $item['TeacherLesson']['title'];
            }
        }
        $this->set('lessons', $lessons);
        return $lessons;
    }
}
