<?php

App::uses('AppController', 'Controller');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class ApiUsersController extends AppController {

    public $uses = array('User', 'Device', 'UserFavorite', 
        'Course', 'Lesson', 'Unit', 'UserCourse');
    public $components = array('JWT', 'Social');
    
    const FB_APP_ID = 874635473067320;
    const IOS_BUNDLE_ID = 'com.necampus';

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $this->autoRender = false;
        $this->response->type('json');
    }

    public function register() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        try{
            if(!$this->request->is('post')){
                $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
                $response->message = "Method not allow!";
                return $this->response->body(json_encode($response));
            }
            $data = $this->request->data;
            $require_fields = [
                'username', 'email', 'password', 'first_name', 'last_name', 'device_id', 'ts', 'hash'
            ];

            // Validate data
            foreach ($require_fields as $field) {
                if(!isset($data[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require!";
                    return $this->response->body(json_encode($response));
                }
            }

            // Validate timestamp and hash
            $res = $this->validateHash($data);
            if($res !== true){
                return $this->response->body(json_encode($res));
            }

            // Validate device_id
            $item = $this->Device->find('first', [
                'conditions' => [
                    'device_id' => $data['device_id']
                ]
            ]);
            if(empty($item)){
                $response->error_code = self::ERROR_INVALID_INPUT;
                $response->message = "Access Denied!";
                return $this->response->body(json_encode($response));
            }

            // Validate max register time per device
            if($item['Device']['register_count'] > Configure::read('device_register_limit')){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Invalid device_id!";
                return $this->response->body(json_encode($response));
            }
            $this->Device->id = $item['Device']['id'];
            $this->Device->saveField('register_count', $item['Device']['register_count'] + 1);
            // Processing create user
            // Check email exists
            $item = $this->User->findByEmail($data['email']);
            if(!empty($item)){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Email already exists";
                return $this->response->body(json_encode($response));
            }
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Invalid email format";
                return $this->response->body(json_encode($response));
            }
            // Check username exists
            $item = $this->User->findByUsername($data['username']);
            if(!empty($item)){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Username already exists";
                return $this->response->body(json_encode($response));
            }
            if(strlen($data['password']) < Configure::read('password_min_length')){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Password length require more than 6 characters";
                return $this->response->body(json_encode($response));
            }
            $user = $data;
            $user['id'] = $this->gen_uuid();
            $user['login_type'] = 'normal';
            if(!isset($user['avatar']) || $user['avatar'] == ''){
                $user['avatar'] = Router::url('/', true) . Configure::read('default_avatar');
            }
            $this->User->create();
            $this->User->save($user);
            $response->data->id = $user['id'];
            $response->data->first_name = $user['first_name'];
            $response->data->last_name = $user['last_name'];
            $response->data->username = $user['username'];
            $response->data->email = $user['email'];
            $response->data->login_type = $user['login_type'];
            $response->data->avatar = $user['avatar'];
            $response->data->access_token = $this->JWT->generateAccessToken($user);

            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
    
    public function login(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        try{
            if(!$this->request->is('post')){
                $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
                $response->message = "Method not allow!";
                return $this->response->body(json_encode($response));
            }
            $data = $this->request->data;
            $require_fields = [
                'username', 'password', 'device_id', 'ts', 'hash'
            ];
            
            // Validate data
            foreach ($require_fields as $field) {
                if(!isset($data[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require!";
                    return $this->response->body(json_encode($response));
                }
            }

            // Validate timestamp and hash
            $res = $this->validateHash($data);
            if($res !== true){
                return $this->response->body(json_encode($res));
            }

            // Validate device_id
            $item = $this->Device->find('first', [
                'conditions' => [
                    'device_id' => $data['device_id']
                ]
            ]);
            if(empty($item)){
                $response->error_code = self::ERROR_INVALID_INPUT;
                $response->message = "Access Denied!";
                return $this->response->body(json_encode($response));
            }
            if(!$this->validateMaximumLoginDaily($item['Device'])){
                $response->error_code = self::ERROR_SYSTEM;
                $response->message = "Login limited!";
                return $this->response->body(json_encode($response));
            }
            
            // Check password
            $item = $this->User->find('first', [
                'contain' => false,
                'conditions' => [
                    'OR' => [
                        'email' => $data['username'],
                        'username' => $data['username']
                    ]
                ]
            ]);
            if(empty($item)){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "User not found";
                return $this->response->body(json_encode($response));
            }
            $user = $item['User'];
            $passwordHasher = new SimplePasswordHasher();
            $pwhash = $passwordHasher->hash($data['password']);
            if($pwhash != $user['password']){
                $response->error_code = self::ERROR_INVALID_PASSWORD;
                $response->message = "Username or password is wrong";
                return $this->response->body(json_encode($response));
            }
            
            // login success
            $response->data->id = $user['id'];
            $response->data->username = $user['username'];
            $response->data->first_name = $user['first_name'];
            $response->data->last_name = $user['last_name'];
            $response->data->email = $user['email'];
            $response->data->login_type = $user['login_type'];
            $response->data->avatar = $user['avatar'];
            $response->data->access_token = $this->JWT->generateAccessToken($user);
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
    
    public function login_facebook() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "success";
        $response->data = new stdClass();
        if (!$this->request->is('post')) {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
            return $this->response->body(json_encode($response));
        }
        $data = $this->request->data;
        $require_fields = [
            'access_token', 'device_id', 'ts', 'hash'
        ];

        // Validate data
        foreach ($require_fields as $field) {
            if(!isset($data[$field])){
                $response->error_code = self::ERROR_MISSING_PARAMS;
                $response->message = "$field require!";
                return $this->response->body(json_encode($response));
            }
        }

        // Validate timestamp and hash
        $res = $this->validateHash($data);
        if($res !== true){
            return $this->response->body(json_encode($res));
        }

        // Validate device_id
        $item = $this->Device->find('first', [
            'conditions' => [
                'device_id' => $data['device_id']
            ]
        ]);
        if(empty($item)){
            $response->error_code = self::ERROR_INVALID_INPUT;
            $response->message = "Access Denied!";
            return $this->response->body(json_encode($response));
        }
        if(!$this->validateMaximumLoginDaily($item['Device'])){
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = "Login limited!";
            return $this->response->body(json_encode($response));
        }
        try {
            $userInfo = $this->Social->getFacebookUserInfo($data['access_token']);
            if ($userInfo) {
                if (isset($userInfo['error'])) {
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid facebook access_token";
                    return $this->response->body(json_encode($response));
                }
                // Validate app_id
                $appInfo = $this->Social->getFacebookAppInfo($data['access_token']);
                if(!$appInfo || isset($appInfo['error']) || $appInfo['id'] != self::FB_APP_ID){
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid facebook access_token";
                    return $this->response->body(json_encode($response));
                }
                $userData = $this->User->findBySocialId($userInfo['id']);
                if (empty($userData)) { //Create new user
                    $user = [];
                    $user['id'] = $this->gen_uuid();
                    $user['login_type'] = 'facebook';
                    $user['social_id'] = $userInfo['id'];
                    $user['first_name'] = $userInfo['first_name'];
                    $user['last_name'] = $userInfo['last_name'];
                    $user['username'] = $userInfo['id'];
                    $user['email'] = isset($userInfo['email']) ? $userInfo['email'] : null;
                    $user['avatar'] = Router::url('/', true) . Configure::read('default_avatar');
                    $user['status'] = 'activated';
                    $this->User->create();
                    $u = $this->User->save(['User' => $user]);
                    if (!$u) {
                        $response->error_code = self::ERROR_SYSTEM;
                        $response->message = "System busy";
                        return $this->response->body(json_encode($response));
                    } else {
                        $response->data->id = $user['id'];
                        $response->data->first_name = $user['first_name'];
                        $response->data->last_name = $user['last_name'];
                        $response->data->username = $user['username'];
                        $response->data->email = $user['email'];
                        $response->data->login_type = $user['login_type'];
                        $response->data->avatar = $user['avatar'];
                        $response->data->access_token = $this->JWT->generateAccessToken($user);
                    }
                } else {
                    $response->data->id = $userData['User']['id'];
                    $response->data->first_name = $userData['User']['first_name'];
                    $response->data->last_name = $userData['User']['last_name'];
                    $response->data->username = $userData['User']['username'];
                    $response->data->email = $userData['User']['email'];
                    $response->data->login_type = $userData['User']['login_type'];
                    $response->data->avatar = $userData['User']['avatar'];
                    $response->data->access_token = $this->JWT->generateAccessToken($userData['User']);
                }
            } else {
                $response->error_code = self::ERROR_INVALID_INPUT;
                $response->message = "Invalid facebook access_token";
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }

        return $this->response->body(json_encode($response));
    }

    public function login_google() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "success";
        $response->data = new stdClass();
        if (!$this->request->is('post')) {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
            return $this->response->body(json_encode($response));
        }
        $data = $this->request->data;
        $require_fields = [
            'access_token', 'device_id', 'ts', 'hash'
        ];

        // Validate data
        foreach ($require_fields as $field) {
            if(!isset($data[$field])){
                $response->error_code = self::ERROR_MISSING_PARAMS;
                $response->message = "$field require!";
                return $this->response->body(json_encode($response));
            }
        }

        // Validate timestamp and hash
        $res = $this->validateHash($data);
        if($res !== true){
            return $this->response->body(json_encode($res));
        }

        // Validate device_id
        $item = $this->Device->find('first', [
            'conditions' => [
                'device_id' => $data['device_id']
            ]
        ]);
        if(empty($item)){
            $response->error_code = self::ERROR_INVALID_INPUT;
            $response->message = "Access Denied!";
            return $this->response->body(json_encode($response));
        }
        if(!$this->validateMaximumLoginDaily($item['Device'])){
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = "Login limited!";
            return $this->response->body(json_encode($response));
        }
        try {
            $userInfo = $this->Social->getGoogleUserInfo($data['access_token']);
            if ($userInfo) {
                if (isset($userInfo['error'])) {
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid access_token";
                    return $this->response->body(json_encode($response));
                }
                $userData = $this->User->findByEmail($userInfo['email']);
                if(empty($userData)){
                    $userData = $this->User->findBySocialId($userInfo['id']);
                }
                if (empty($userData)) { //Create new user
                    $user = [];
                    $user['id'] = $this->gen_uuid();
                    $user['login_type'] = 'google';
                    $user['social_id'] = $userInfo['id'];
                    $user['first_name'] = $userInfo['givenName'];
                    $user['last_name'] = $userInfo['familyName'];
                    $user['username'] = $userInfo['email'];
                    $user['email'] = $userInfo['email'];
                    $user['avatar'] = isset($userInfo['picture']) ? $userInfo['picture'] : Router::url('/', true) . Configure::read('default_avatar');
                    $user['status'] = 'activated';
                    $this->User->create();
                    $u = $this->User->save(['User' => $user]);
                    if (!$u) {
                        $response->error_code = self::ERROR_SYSTEM;
                        $response->message = "System busy";
                        return $this->response->body(json_encode($response));
                    } else {
                        $response->data->id = $user['id'];
                        $response->data->first_name = $user['first_name'];
                        $response->data->last_name = $user['last_name'];
                        $response->data->username = $user['username'];
                        $response->data->email = $user['email'];
                        $response->data->login_type = $user['login_type'];
                        $response->data->avatar = $user['avatar'];
                        $response->data->access_token = $this->JWT->generateAccessToken($user);
                    }
                } else {
                    $response->data->id = $userData['User']['id'];
                    $response->data->first_name = $userData['User']['first_name'];
                    $response->data->last_name = $userData['User']['last_name'];
                    $response->data->username = $userData['User']['username'];
                    $response->data->email = $userData['User']['email'];
                    $response->data->login_type = $userData['User']['login_type'];
                    $response->data->avatar = $userData['User']['avatar'];
                    $response->data->access_token = $this->JWT->generateAccessToken($userData['User']);
                }
            } else {
                $response->error_code = self::ERROR_INVALID_INPUT;
                $response->message = "Invalid access_token";
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }

        return $this->response->body(json_encode($response));
    }

    public function login_apple(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "success";
        $response->data = new stdClass();
        if (!$this->request->is('post')) {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
            return $this->response->body(json_encode($response));
        }
        $data = $this->request->data;
        $require_fields = [
            'apple_code', 'device_id', 'ts', 'hash'
        ];

        // Validate data
        foreach ($require_fields as $field) {
            if(!isset($data[$field])){
                $response->error_code = self::ERROR_MISSING_PARAMS;
                $response->message = "$field require!";
                return $this->response->body(json_encode($response));
            }
        }

        // Validate timestamp and hash
        $res = $this->validateHash($data);
        if($res !== true){
            return $this->response->body(json_encode($res));
        }

        // Validate device_id
        $item = $this->Device->find('first', [
            'conditions' => [
                'device_id' => $data['device_id']
            ]
        ]);
        if(empty($item)){
            $response->error_code = self::ERROR_INVALID_INPUT;
            $response->message = "Access Denied!";
            return $this->response->body(json_encode($response));
        }
        if(!$this->validateMaximumLoginDaily($item['Device'])){
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = "Login limited!";
            return $this->response->body(json_encode($response));
        }
        try {
            $userInfo = $this->Social->getAppleUserInfo($data['apple_code']);
            if ($userInfo) {
                if (!isset($userInfo['id_token'])) {
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid apple_code";
                    return $this->response->body(json_encode($response));
                }
                
                $claims = explode('.', $userInfo['id_token'])[1];
                $claims = json_decode(base64_decode($claims), true);

                // Validate app
                if($claims['aud'] != self::IOS_BUNDLE_ID){// return error
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid aud";
                    return $this->response->body(json_encode($response));
                }
                $email = (isset($claims['email'])) ? $claims['email'] : NULL;
                $socialAccountId = $claims['sub'];
                if($email != null){
                    $userData = $this->User->findByEmail($email);
                    if(empty($userData)){
                        $userData = $this->User->findBySocialId($socialAccountId);
                    }
                } else {
                    $userData = $this->User->findBySocialId($socialAccountId);
                }
                if (empty($userData)) { //Create new user
                    $user = [];
                    $user['id'] = $this->gen_uuid();
                    $user['login_type'] = 'apple';
                    $user['social_id'] = $socialAccountId;
                    $user['first_name'] = '';
                    $user['last_name'] = '';
                    $user['username'] = ($email != null) ? $email : $socialAccountId;
                    $user['email'] = $email;
                    $user['avatar'] = Router::url('/', true) . Configure::read('default_avatar');
                    $user['status'] = 'activated';
                    $this->User->create();
                    $u = $this->User->save($user);
                    if (!$u) {
                        $response->error_code = self::ERROR_SYSTEM;
                        $response->message = "System busy";
                        return $this->response->body(json_encode($response));
                    } else {
                        $response->data->id = $user['id'];
                        $response->data->first_name = $user['first_name'];
                        $response->data->last_name = $user['last_name'];
                        $response->data->username = $user['username'];
                        $response->data->email = $user['email'];
                        $response->data->login_type = $user['login_type'];
                        $response->data->avatar = $user['avatar'];
                        $response->data->access_token = $this->JWT->generateAccessToken($user);
                    }
                } else {
                    $response->data->id = $userData['User']['id'];
                    $response->data->first_name = $userData['User']['first_name'];
                    $response->data->last_name = $userData['User']['last_name'];
                    $response->data->username = $userData['User']['username'];
                    $response->data->email = $userData['User']['email'];
                    $response->data->login_type = $userData['User']['login_type'];
                    $response->data->avatar = $userData['User']['avatar'];
                    $response->data->access_token = $this->JWT->generateAccessToken($userData['User']);
                }
            } else {
                $response->error_code = self::ERROR_INVALID_INPUT;
                $response->message = "Invalid access_token";
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }

        return $this->response->body(json_encode($response));
    }
    
    private function validateMaximumLoginDaily($device){
        $last_day = date('Ymd', $device['last_time']);
        $current_day = date('Ymd');
        $this->Device->id = $device['id'];
        if($last_day == $current_day){
            if($device['daily_login_count'] > Configure::read('device_daily_login_limit')){
                $this->Device->saveField('daily_login_count', $device['daily_login_count'] + 1);
                return false;
            } else {
                $this->Device->saveField('daily_login_count', $device['daily_login_count'] + 1);
                return true;
            }
        } else {
            $device['last_time'] = time();
            $device['daily_login_count'] = 1;
            $this->Device->save($device);
            return true;
        }
    }
    
    public function favorites(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        try{
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            if($this->request->is(['post', 'delete'])){ // create favorites
                $require_fields = ['entity_id','entity_type'];
                $data = $this->request->data;
                foreach ($require_fields as $field) {
                    if(!isset($data[$field])){
                        $response->error_code = self::ERROR_MISSING_PARAMS;
                        $response->message = "$field require";
                        return $this->response->body(json_encode($response));
                    }
                }
                if(!in_array($data['entity_type'], self::ALLOW_ENTITY_TYPES)){
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid entity_type";
                    return $this->response->body(json_encode($response));
                }

                $item = $this->UserFavorite->find('first', [
                    'contain' => false,
                    'conditions' => [
                        'UserFavorite.user_id' => $user['id'],
                        'UserFavorite.entity_id' => $data['entity_id'],
                        'UserFavorite.entity_type' => $data['entity_type'],
                    ]
                ]);

                if($this->request->is('post')){ // Insert new favorite
                    if(empty($item)){ 
                       $this->UserFavorite->create();
                       $this->UserFavorite->save([
                           'user_id' => $user['id'],
                           'entity_id' => $data['entity_id'],
                           'entity_type' => $data['entity_type'],
                       ]);
                       
                       // Add favorite count
                       if($data['entity_type'] == 'lesson'){
                           $this->Lesson->updateAll(array('Lesson.favorite_count' => 'Lesson.favorite_count + 1'), array('Lesson.id' => $data['entity_id']));
                       }
                    }
                } else if($this->request->is('delete')){ // remove from favorite
                    if(!empty($item)){
                        $this->UserFavorite->delete($item['UserFavorite']['id']);
                        if($data['entity_type'] == 'lesson'){
                            $this->Lesson->updateAll(array('Lesson.favorite_count' => 'Lesson.favorite_count - 1'), array('Lesson.id' => $data['entity_id']));
                        }
                    }
                }
                return $this->response->body(json_encode($response));
            } else if($this->request->is('get')) { // list favorites
                $response->data = [];
                $require_fields = ['entity_type'];
                $query = $this->request->query;
                foreach ($require_fields as $field) {
                    if(!isset($query[$field])){
                        $response->error_code = self::ERROR_MISSING_PARAMS;
                        $response->message = "$field require";
                        return $this->response->body(json_encode($response));
                    }
                }
                
                if(!isset($query['limit']) || $query['limit'] <= 0){
                    $query['limit'] = 20;
                }
                if(!isset($query['page']) || $query['page'] <= 0){
                    $query['page'] = 1;
                }
                
                $items = $this->UserFavorite->find('all', [
                    'conditions' => [
                        'user_id' => $user['id'],
                        'entity_type' => $query['entity_type']
                    ],
                    'order' => ['id' => 'DESC'],
                    'limit' => $query['limit'],
                    'page' => $query['page']
                ]);
                if(!empty($items)){
                    $ids = [];
                    foreach($items as $item){
                        $ids[] = $item['UserFavorite']['entity_id'];
                    }
                    switch ($query['entity_type']){
                        case 'course':
                            $courses = $this->Course->find('all', [
                                'contain' => ['CourseTag' => 'Tag'],
                                'conditions' => [
                                    'Course.id' => $ids
                                ],
                                'order' => 'FIELD(Course.id, ' . implode(",", $ids) . ')',
                                'limit' => $query['limit'],
                                'page' => $query['page'],
                            ]);
                            if(!empty($courses)){
                                foreach($courses as $course){
                                    $row = [
                                        'id' => $course['Course']['id'],
                                        'title' => $course['Course']['title'],
                                        'thumb' => $course['Course']['thumb'],
                                        'short_description' => $course['Course']['short_description'],
                                        'description' => $course['Course']['description'],
                                        'tags' => $this->_getTagObjects($course['CourseTag'])
                                    ];
                                    $response->data[] = $row;
                                }
                            }
                            break;
                        case 'lesson':
                            $lessons = $this->Lesson->find('all', [
                                'contain' => ['LessonTag' => 'Tag'],
                                'conditions' => [
                                    'Lesson.id' => $ids
                                ],
                                'order' => 'FIELD(Lesson.id, ' . implode(",", $ids) . ')',
                                'limit' => $query['limit'],
                                'page' => $query['page'],
                            ]);
                            if(!empty($lessons)){
                                foreach($lessons as $lesson){
                                    $row = [
                                        'id' => $lesson['Lesson']['id'],
                                        'course_id' => $lesson['Lesson']['course_id'],
                                        'chapter_id' => $lesson['Lesson']['chapter_id'],
                                        'title' => $lesson['Lesson']['title'],
                                        'thumb' => $lesson['Lesson']['thumb'],
                                        'short_description' => $lesson['Lesson']['short_description'],
                                        'tags' => $this->_getTagObjects($lesson['LessonTag'])
                                    ];
                                    $response->data[] = $row;
                                }
                            }
                            break;
                        case 'unit':
                            $units = $this->Unit->find('all', [
                                'contain' => false,
                                'conditions' => [
                                    'Unit.id' => $ids
                                ],
                                'order' => 'FIELD(Unit.id, ' . implode(",", $ids) . ')',
                                'limit' => $query['limit'],
                                'page' => $query['page'],
                            ]);
                            if(!empty($units)){
                                foreach($units as $unit){
                                    $row = [
                                        'id' => $unit['Unit']['id'],
                                        'lesson_id' => $unit['Unit']['lesson_id'],
                                        'url' => $unit['Unit']['url'],
                                        'zip_url' => $unit['Unit']['zip_url'],
                                        'title' => $unit['Unit']['title'],
                                        'thumb' => $unit['Unit']['thumb'],
                                        'short_description' => $unit['Unit']['short_description'],
                                    ];
                                    $response->data[] = $row;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    
                }
            }
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
    
    public function unfavorites(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        try{
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            if($this->request->is(['post', 'delete'])){ // delete favorites
                $require_fields = ['entity_id','entity_type'];
                $data = $this->request->data;
                foreach ($require_fields as $field) {
                    if(!isset($data[$field])){
                        $response->error_code = self::ERROR_MISSING_PARAMS;
                        $response->message = "$field require";
                        return $this->response->body(json_encode($response));
                    }
                }
                if(!in_array($data['entity_type'], self::ALLOW_ENTITY_TYPES)){
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Invalid entity_type";
                    return $this->response->body(json_encode($response));
                }

                $item = $this->UserFavorite->find('first', [
                    'contain' => false,
                    'conditions' => [
                        'UserFavorite.user_id' => $user['id'],
                        'UserFavorite.entity_id' => $data['entity_id'],
                        'UserFavorite.entity_type' => $data['entity_type'],
                    ]
                ]);
                
                if(!empty($item)){
                    $this->UserFavorite->delete($item['UserFavorite']['id']);
                    if($data['entity_type'] == 'lesson'){
                        $this->Lesson->updateAll(array('Lesson.favorite_count' => 'Lesson.favorite_count - 1'), array('Lesson.id' => $data['entity_id']));
                    }
                }
            }
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
    
    public function profile(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        try{
            if(!$this->request->is('get')){
                $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
                $response->message = 'Method not allow!';
                return $this->response->body(json_encode($response));
            }
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            
            $u = $this->User->find('first', [
                'contain' => false,
                'fields' => ['id', 'username', 'first_name', 'last_name', 'email', 'phone', 'social_id', 'login_type', 'avatar', 'address', 'status', 'created'],
                'conditions' => [
                    'id' => $user['id']
                ]
            ]);
            if(!$u){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "User not found";
            }
            
            $response->data = $u['User'];
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
    
    public function update_profile(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        try{
            if(!$this->request->is('post')){
                $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
                $response->message = 'Method not allow!';
                return $this->response->body(json_encode($response));
            }
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            
            $u = $this->User->find('first', [
                'contain' => false,
                'conditions' => [
                    'id' => $user['id']
                ]
            ]);
            if(!$u){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "User not found";
                return $this->response->body(json_encode($response));
            }
            // Process update data
            $data = $this->request->data;
            $basic_fields = ['first_name', 'last_name', 'phone', 'avatar', 'address'];
            foreach ($basic_fields as $field) {
                if(isset($data[$field])){
                    $u['User'][$field] = $data[$field];
                }
            }
            // Check email exit
            if(isset($data['email'])){
                $mail_user = $this->User->findByEmail($data['email']);
                if(!empty($mail_user) && $mail_user['User']['id'] != $u['User']['id']){
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = "Email already exist!";
                    return $this->response->body(json_encode($response));
                }
            }
            // Check update password
            if(isset($data['current_password']) && isset($data['new_password']) && isset($data['confirm_password'])){
                if($data['new_password'] == $data['current_password']){
                    $response->error_code = self::ERROR_INVALID_INPUT;
                    $response->message = 'Password not updated';
                    return $this->response->body(json_encode($response));
                }
                $passwordHasher = new SimplePasswordHasher();
                $pwhash = $passwordHasher->hash($data['current_password']);
                if($u['User']['password'] != $pwhash){
                    $response->error_code = self::ERROR_INVALID_PASSWORD;
                    $response->message = 'Invalid current password';
                    return $this->response->body(json_encode($response));
                }
                
                if(strlen($data['new_password']) < Configure::read('password_min_length')){
                    $response->error_code = self::ERROR_SYSTEM;
                    $response->message = "Password length require more than " . Configure::read('password_min_length') . " characters";
                    return $this->response->body(json_encode($response));
                }
                if($data['new_password'] != $data['confirm_password']){
                    $response->error_code = self::ERROR_INVALID_PASSWORD;
                    $response->message = 'Confirm password not match!';
                    return $this->response->body(json_encode($response));
                }
                
                $u['User']['password'] = $data['new_password'];
                $u['User']['modified'] = date('Y-m-d H:i:s');
                $this->User->id = $u['User']['id'];
                $this->User->save($u);
            }
            unset($u['User']['password']);
            unset($u['User']['modified']);
            $this->User->id = $u['User']['id'];
            $this->User->save($u);
            $response->data = $u['User'];
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }

    
    public function mycourses(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        try{
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            
            $response->data = [];
            $query = $this->request->query;

            if(!isset($query['limit']) || $query['limit'] <= 0){
                $query['limit'] = 20;
            }
            if(!isset($query['page']) || $query['page'] <= 0){
                $query['page'] = 1;
            }

            $items = $this->UserCourse->find('all', [
                'contain' => ['Course' => ['CourseTag' => 'Tag']],
                'conditions' => [
                    'user_id' => $user['id'],
                ],
                'order' => ['last_view' => 'DESC'],
                'limit' => $query['limit'],
                'page' => $query['page']
            ]);
            if(!empty($items)){
                foreach($items as $item){
                    if(!empty($item['Course']) && $item['Course']['id'] > 0){
                        $row = [
                            'id' => $item['Course']['id'],
                            'title' => $item['Course']['title'],
                            'thumb' => $item['Course']['thumb'],
                            'short_description' => $item['Course']['short_description'],
                            'description' => $item['Course']['description'],
                            'viewed_lesson' => $item['UserCourse']['lesson_count'],
                            'total_lesson' => $item['Course']['total_lesson'],
                            'tags' => $this->_getTagObjects($item['Course']['CourseTag'])
                        ];
                        $response->data[] = $row;
                    }
                }
            }
            return $this->response->body(json_encode($response));
        } catch (Exception $ex) {
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $this->response->body(json_encode($response));
        }
    }
}
