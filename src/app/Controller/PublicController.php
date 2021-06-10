<?php

App::uses('AppController', 'Controller');

class PublicController extends AppController
{

    public $uses = array('Device', 'Lesson', 'Config', 'Teacher', 'TeacherLesson', 'TeacherScene');
    public $components = array('S3');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function isAuthorized($user)
    {
        return TRUE;
    }

    public function index()
    {
        $this->layout = 'default/default';
        $query = $this->request->query;
    }


    public function admin_login()
    {
        if ($this->Auth->loggedIn()) {
            if($this->auth_user['type'] == 'admin'){
                return $this->redirect(array('controller' => 'courses', 'action' => 'index', 'admin' => true));
            } else {
                $this->Session->destroy();
            }
        }
        $this->layout = 'admin/login';
        $this->set('title_for_layout', __('Admin Login'));
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Auth->login()) {
                $auth_user = $this->Auth->user();
                return $this->redirect(array('controller' => 'courses', 'action' => 'index', 'admin' => true));
            } else {
                $this->Session->setFlash(__('Invalid username or password or not active. Please try again'), 'default', array('class' => 'alert alert-danger'));
            }
        }
    }

    public function admin_logout()
    {
        $this->Session->destroy();
        return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => TRUE));
    }
    
    public function teacher_login()
    {
        if ($this->Auth->loggedIn()) {
            $user = $this->Auth->user();
            if($user['type'] == 'admin'){
                return $this->redirect(array('controller' => 'public', 'action' => 'logout', 'teacher' => true));
            } else {
                return $this->redirect(array('controller' => 'lessons', 'action' => 'index', 'teacher' => true));
            }
        }
        $this->layout = 'admin/login';
        $this->set('title_for_layout', __('Teacher Login'));
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Auth->login()) {
                $user = $this->Auth->user();
                $user['type'] = 'teacher';
                $this->Auth->login($user);
                return $this->redirect(array('controller' => 'lessons', 'action' => 'index', 'teacher' => true));
            } else {
                $this->Session->setFlash(__('Invalid username or password or not active. Please try again'), 'default', array('class' => 'alert alert-danger'));
            }
        }
    }

    public function teacher_logout()
    {
        $this->Session->destroy();
        return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => TRUE));
    }
    
    public function config(){
        $this->autoRender = false;
        $this->response->type('json');
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = 'success';
        
        $query = $this->request->query;
        $require_fields = ['device_id', 'platform', 'ts', 'hash'];
        foreach ($require_fields as $key => $value) {
            if(!isset($query[$value])){
                $response->error_code = self::ERROR_MISSING_PARAMS;
                $response->message = "Missing $value!";
                return $this->response->body(json_encode($response));
            }
        }
        
        $res = $this->validateHash($query);
        if($res !== true){
            return $this->response->body(json_encode($res));
        }
        // Validate device length
        if(strlen($query['device_id']) < 10){
            $response->error_code = self::ERROR_INVALID_INPUT;
            $response->message = "Invalid device_id!";
            return $this->response->body(json_encode($response));
        }
        // Processing device
        $item = $this->Device->findByDeviceId($query['device_id']);
        if(empty($item)){ // new device, insert to db
            $this->Device->create();
            $this->Device->save([
                'device_id' => $query['device_id'],
                'last_time' => time(),
                'register_count' => 0,
                'daily_login_count' => 0
            ]);
        }
        $response->data = [];
        $configs = $this->Config->find('all', [
            'conditions' => ['platform' => $query['platform']]
        ]);
        if(!empty($configs)){
            foreach ($configs as $config) {
                $response->data[$config['Config']['key']] = $config['Config']['value'];
            }
        }
        
        return $this->response->body(json_encode($response));
    }
    
    public function lesson_detail(){
        $this->layout = false;
        $id = $this->params['id'];
        
        $item = $this->Lesson->find('first', [
            'contain' => false,
            'conditions' => [
                'Lesson.id' => $id
            ]
        ]);
        
        if(empty($item)){
            $this->autoRender = false;
            echo '<h3>URL you requested not found!</h3>';
            return;
        }
        $qrtext = Router::url('/', true) . 'lesson/' . $id;
        
        $share_image = "https://chart.googleapis.com/chart?cht=qr&chs=250&chl=". urlencode($qrtext);
        $preview_image = "https://chart.googleapis.com/chart?cht=qr&chs=500&chl=". urlencode($qrtext);
        $this->set('share_image', $share_image);
        $this->set('preview_image', $preview_image);
        $this->set('item', $item);
        $is_mobile = false;
        $user_agent = env('HTTP_USER_AGENT');
        $os = $this->getOS($user_agent);
        if(in_array($os, ['iPhone', 'iPod', 'iPad', 'Android', 'BlackBerry', 'Mobile'])){
            $is_mobile = true;
        }
        $this->set('is_mobile', $is_mobile);
    }
    
    public function info(){
        $this->layout = false;
        $id = $this->params['id'];
        
        $item = $this->Teacher->find('first', [
            'contain' => false,
            'conditions' => [
                'id' => $id
            ]
        ]);
        
        if(empty($item)){
            $this->autoRender = false;
            echo '<h3>URL you requested not found!</h3>';
            return;
        }
        $qrtext = $this->_generateTeacherShareUrl($id);
        
        $share_image = "https://chart.googleapis.com/chart?cht=qr&chs=250&chl=". urlencode($qrtext);
        $preview_image = "https://chart.googleapis.com/chart?cht=qr&chs=500&chl=". urlencode($qrtext);
        $this->set('share_image', $share_image);
        $this->set('preview_image', $preview_image);
        $this->set('item', $item);
        $is_mobile = false;
        $user_agent = env('HTTP_USER_AGENT');
        $os = $this->getOS($user_agent);
        if(in_array($os, ['iPhone', 'iPod', 'iPad', 'Android', 'BlackBerry', 'Mobile'])){
            $is_mobile = true;
        }
        $this->set('is_mobile', $is_mobile);
    }
    
    public function teacher_lesson_detail(){
        $this->layout = false;
        $id = $this->params['id'];
        $user_id = $this->params['user_id'];
        
        $item = $this->TeacherLesson->find('first', [
            'contain' => false,
            'conditions' => [
                'TeacherLesson.id' => $id
            ]
        ]);
        
        if(empty($item)){
            $this->autoRender = false;
            echo '<h3>URL you requested not found!</h3>';
            return;
        }
        
        $qrtext = $this->_generateLessonShareUrl($user_id, $id);
        
        $share_image = "https://chart.googleapis.com/chart?cht=qr&chs=250&chl=". urlencode($qrtext);
        $preview_image = "https://chart.googleapis.com/chart?cht=qr&chs=500&chl=". urlencode($qrtext);
        $this->set('share_image', $share_image);
        $this->set('preview_image', $preview_image);
        $this->set('user_id', $user_id);
        $this->set('item', $item);
        $is_mobile = false;
        $user_agent = env('HTTP_USER_AGENT');
        $os = $this->getOS($user_agent);
        if(in_array($os, ['iPhone', 'iPod', 'iPad', 'Android', 'BlackBerry', 'Mobile'])){
            $is_mobile = true;
        }
        $this->set('is_mobile', $is_mobile);
    }
    
    
    public function scene_detail(){
        $this->layout = false;
        $id = $this->params['id'];
        $user_id = $this->params['user_id'];
        
        $item = $this->TeacherScene->find('first', [
            'contain' => false,
            'conditions' => [
                'TeacherScene.id' => $id
            ]
        ]);
        
        if(empty($item)){
            $this->autoRender = false;
            echo '<h3>URL you requested not found!</h3>';
            return;
        }
        
        $qrtext = $this->_generateSceneShareUrl($user_id, $id);
        
        $share_image = "https://chart.googleapis.com/chart?cht=qr&chs=250&chl=". urlencode($qrtext);
        $preview_image = "https://chart.googleapis.com/chart?cht=qr&chs=500&chl=". urlencode($qrtext);
        $this->set('share_image', $share_image);
        $this->set('preview_image', $preview_image);
        $this->set('user_id', $user_id);
        $this->set('item', $item);
        $is_mobile = false;
        $user_agent = env('HTTP_USER_AGENT');
        $os = $this->getOS($user_agent);
        if(in_array($os, ['iPhone', 'iPod', 'iPad', 'Android', 'BlackBerry', 'Mobile'])){
            $is_mobile = true;
        }
        $this->set('is_mobile', $is_mobile);
    }
    
    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuv!@#$%^&*wxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function upload(){
        $this->autoRender = false;
        $this->response->type('json');
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = 'success';
        
        $user = $this->getAuthUser();
        
        //$user = ['id' => '123'];
        
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        
        // validate hash
        $data = $this->request->data;
        if(!isset($data['ts']) || !isset($data['hash']) || !isset($_FILES['file']) || empty($_FILES['file'])){
            $response->error_code = self::ERROR_MISSING_PARAMS;
            $response->message = "Missing file";
            return $this->response->body(json_encode($response));
        }
        
        $params = [
            'user_id' => $user['id'],
            'ts' => $data['ts'],
            'hash' => $data['hash']
        ];
        $res = $this->validateHash($params);
        if($res !== true){
            return $this->response->body(json_encode($res));
        }
        // Upload file
        list($s3_key, $url) = $this->S3->upload($_FILES['file'], ['type' => 'media', 'user_id' => $user['id']]);
        if($url){
            $response->data = new stdClass();
            $response->data->s3_key = $s3_key;
            $response->data->url = $url;
        } else {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = "Upload file error!";
        }
        return $this->response->body(json_encode($response));
    }
}
