<?php

App::uses('AppController', 'Controller');
App::uses('GearmanQueue', 'Gearman.Client');

class ApiTeachersController extends AppController {

    public $uses = array('Teacher', 'UserTeacher', 'TeacherLesson', 'TeacherLessonSkill',
        'UserTeacherLesson', 'RecentView', 'TeacherScene', 'UserTeacherScene',
        'Feedback', 'Media');
    public $components = array('Google');

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
        try{
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            $teacher_ids = $this->get_followed_teacher_ids($user['id']);
            if(empty($teacher_ids)){
                return $this->response->body(json_encode($response));
            }
            
            $query = $this->request->query;
            if(!isset($query['limit']) || $query['limit'] <= 0){
                $query['limit'] = 20;
            } 
            if(!isset($query['page']) || $query['page'] <= 0){
                $query['page'] = 1;
            }
            $items = $this->Teacher->find('all', [
                'contain' => ['TeacherSkill' => [
                    'fields' => ['id', 'name', 'sort_order', 'lesson_count'],
                    'order' => ['sort_order' => 'DESC']
                    ]],
                'conditions' => [
                    'Teacher.id' => $teacher_ids,
                ],
                'order' => ['Teacher.name' => 'ASC'],
                'limit' => $query['limit'],
                'page' => $query['page']
            ]);
            
            if(!empty($items)){
                foreach($items as $item){
                    $item['Teacher']['is_followed'] = $this->isFollowed($user['id'], $item['Teacher']['id']);
                    $item['Teacher']['total_lessons'] = $this->TeacherLesson->find('count', ['conditions' => [
                        'teacher_id' => $item['Teacher']['id']
                    ]]);
                    $item['Teacher']['skills'] = $item['TeacherSkill'];
                    $response->data[] = $item['Teacher'];
                }
            }
            
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }
        return $this->response->body(json_encode($response));
    }
    
    public function api_detail() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $id = $this->params['id'];
        $item = $this->Teacher->find('first', [
            'contain' => ['TeacherSkill' => [
                    'fields' => ['id', 'name', 'sort_order', 'lesson_count'],
                    'order' => ['sort_order' => 'DESC']
                    ]],
            'conditions' => [
                'Teacher.id' => $id
            ]
        ]);
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        if (!empty($item)) {
            $item['Teacher']['skills'] = $item['TeacherSkill'];
            if(!empty($item['Teacher']['skills'])){
                foreach ($item['Teacher']['skills'] as $key => $skill){
                    $skill['lesson_learned'] = $this->getTeacherLessonLearned($user['id'], $item['Teacher']['id'], $skill['id']);
                    $item['Teacher']['skills'][$key] = $skill;
                }
            }
            $response->data = json_decode(json_encode($item['Teacher']));
            $response->data->is_followed = $this->isFollowed($user['id'], $item['Teacher']['id']);
            $response->data->share_url = $this->_generateTeacherShareUrl($item['Teacher']['id']);
        }
        return $this->response->body(json_encode($response));
    }
    
    
    public function api_lesson_detail() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $id = $this->params['id'];
        $item = $this->TeacherLesson->find('first', [
            'conditions' => [
                'TeacherLesson.id' => $id
            ]
        ]);
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        if (!empty($item)) {
            $item['TeacherLesson']['share_url'] = $this->_generateLessonShareUrl($user['id'], $id);
            $response->data = json_decode(json_encode($item['TeacherLesson']));
            $response->data->scenes = $this->getTeacherScenes($user['id'], $id);
        }
        
        GearmanQueue::execute('recent_view', [
            'user_id' => $user['id'],
            'teacher_lesson_id' => $id,
        ]);
        return $this->response->body(json_encode($response));
    }
    
    
    public function api_follow(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        if($this->request->is('post', 'put')){
            $data = $this->request->data;
            $require_fields = ['teacher_id', 'type'];
            foreach ($require_fields as $field){
                if(!isset($data[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require";
                    return $this->response->body(json_encode($response));
                }
            }
            $row_data = [
                    'user_id' => $user['id'],
                    'teacher_id' => $data['teacher_id']
                ];
            $item = $this->UserTeacher->find('first', [
                    'conditions' => $row_data
                ]);
            if($data['type'] == 'follow'){
                if(empty($item)){
                    $this->UserTeacher->create();
                    $this->UserTeacher->save($row_data);
                    
                    // Update total follow
                    $this->Teacher->updateAll(array('Teacher.total_follow' => 'Teacher.total_follow + 1'), array('Teacher.id' => $data['teacher_id']));
                }
            } else if($data['type'] == 'unfollow'){
                if(!empty($item)){
                    $this->UserTeacher->deleteAll($row_data);
                    $this->Teacher->updateAll(array('Teacher.total_follow' => 'Teacher.total_follow - 1'), array('Teacher.id' => $data['teacher_id']));
                }
            }
            // Update cache
            
            $cacheKey = "followed_teacher_ids_{$user['id']}";
            Cache::delete($cacheKey);
            $this->get_followed_teacher_ids($user['id']);
        } else {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
        }
        return $this->response->body(json_encode($response));
    }
    
    public function api_skills(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        if($this->request->is('get')){
            $query = $this->request->query;
            $require_fields = ['teacher_id'];
            foreach ($require_fields as $field){
                if(!isset($query[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require";
                    return $this->response->body(json_encode($response));
                }
            }
            $teacher = $this->Teacher->findById($query['teacher_id']);
            
            if(!empty($teacher['TeacherSkill'])){
                foreach($teacher['TeacherSkill'] as $skill){
                    $response->data[] = [
                        'id' => $skill['id'],
                        'name' => $skill['name'],
                        'lesson_count' => $skill['lesson_count'],
                        'lesson_learned' => $this->getTeacherLessonLearned($user['id'], $query['teacher_id'], $skill['id'])
                    ];
                }
            }
        } else {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
        }
        return $this->response->body(json_encode($response));
    }
    public function api_lessons(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        if($this->request->is('get')){
            $query = $this->request->query;
            $require_fields = ['type'];
            foreach ($require_fields as $field){
                if(!isset($query[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require";
                    return $this->response->body(json_encode($response));
                }
            }
            $conditions = [];
            if(isset($query['teacher_id'])){
                $conditions['teacher_id'] = $query['teacher_id'];
            } else {
                //$conditions['teacher_id'] = $this->get_followed_teacher_ids($user['id']);
            }
            if(isset($query['skill_id'])){
                $items = $this->TeacherLessonSkill->find('all', [
                    'fields' => ['teacher_lesson_id'],
                    'conditions' => [
                        'teacher_skill_id' => $query['skill_id']
                    ]
                ]);
                $ids = [];
                if(!empty($items)){
                    foreach($items as $item){
                        $ids[] = $item['TeacherLessonSkill']['teacher_lesson_id'];
                    }
                }
                $conditions['TeacherLesson.id'] = $ids;
            }
            if(!isset($query['limit']) || $query['limit'] <= 0){
                $query['limit'] = 20;
            }
            if(!isset($query['page']) || $query['page'] <= 0){
                $query['page'] = 1;
            }
            $order = ['TeacherLesson.id' => 'DESC'];
            switch ($query['type']){
                case 'recents': // continue learning
                    // Update conditions and orders
                    $item = $this->RecentView->find('first', [
                        'conditions' => ['user_id' => $user['id']]
                    ]);
                    if(empty($item)){
                        return $this->response->body(json_encode($response));
                    }
                    $recents = json_decode($item['RecentView']['teacher_lessons'], true);
                    if(!$recents){
                        return $this->response->body(json_encode($response));
                    }
                    $conditions['TeacherLesson.id'] = $recents;
                    $order = 'FIELD(TeacherLesson.id, ' . implode(",", $recents) . ')';
                    break;
                case 'new_releases':
                    if(!isset($conditions['teacher_id'])){
                        $conditions['teacher_id'] = $this->get_followed_teacher_ids($user['id']);
                    }
                    break;
                default:
                    break;
            }
            $items = $this->TeacherLesson->find('all', [
                'contain' => ['TeacherLessonSkill'],
                'conditions' => $conditions,
                'order' => $order,
                'limit' => $query['limit'],
                'page' => $query['page'],
            ]);
            if(!empty($items)){
                foreach ($items as $item){
                    $item['TeacherLesson']['skill_count'] = count($item['TeacherLessonSkill']);
                    $item['TeacherLesson']['share_url'] = $this->_generateLessonShareUrl($user['id'], $item['TeacherLesson']['id']);
                    $response->data[] = $item['TeacherLesson'];
                }
            }
        } else {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
        }
        return $this->response->body(json_encode($response));
    }
    
    public function api_scene() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        
        $id = $this->params['id'];
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }

        if($this->request->is('post')){ // Create submission
            $scene = $this->TeacherScene->findById($id);
            if(empty($scene)){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "Scene not found!";
                return $this->response->body(json_encode($response));
            }
            $item = $this->UserTeacherScene->find('first', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user['id'],
                    'teacher_scene_id' => $id
                ],
            ]);
            if(empty($item)){
                $item = ['UserTeacherScene' => [
                    'user_id' => $user['id'],
                    'teacher_lesson_id' => $scene['TeacherScene']['teacher_lesson_id'],
                    'teacher_scene_id' => $id,
                    'answer' => '',
                    'is_done' => false
                ]];
            }
            $data = $this->request->data;
            if($scene['TeacherScene']['type'] != 'view'){
                if(!isset($data['type']) || !in_array($data['type'], ['text', 'audio']) || !isset($data['value']) || $data['value'] == ""){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "Missing type or value!";
                    return $this->response->body(json_encode($response));
                }
            }
            
            if(isset($data['type']) && isset($data['value']) && $data['value'] != ""){
                $answer_text = $data['value'];
                $answer = [];
                if($item['UserTeacherScene']['answer'] != ''){
                    $answer = json_decode($item['UserTeacherScene']['answer'], true);
                }
                if(!$answer){
                    $answer = [];
                }

                $answers = explode("|", $answer_text);
                if(!empty($answers)){
                    foreach($answers as $val){
                        $answer[] = [
                            'source' => 'user',
                            'type' => $data['type'],
                            'value' => $val,
                            'created' => time()
                        ];
                    }
                }
                
                // Feedback
                if($data['type'] == 'audio'){
                    if(strpos($data['value'], 'http') !== 0){
                        $response->error_code = self::ERROR_INVALID_INPUT;
                        $response->message = "Invalid audio url!";
                        return $this->response->body(json_encode($response));
                    }
                    $item['UserTeacherScene']['is_done'] = true; // auto done
                    $answer_text = $this->Google->getTextFromAudio($data['value']);
                }
                $feedback = $this->getFeedbackFromAnswer($id, $answer_text);
                if($feedback != false){
                    $answer[] = [
                        'source' => 'feedback',
                        'type' => 'text',
                        'feedback_type' => $feedback['type'],
                        'value' => $feedback['feedback'],
                        'created' => time()
                    ];
                    if($feedback['type'] == 'right'){
                        $item['UserTeacherScene']['is_done'] = true;
                    }
                    $response->data->feedback_type = $feedback['type'];
                    $response->data->feedback = $feedback['feedback'];
                }
                
                $item['UserTeacherScene']['answer'] = json_encode($answer);
            }
            
            if(in_array($scene['TeacherScene']['type'], ['view', 'submission'])){
                $item['UserTeacherScene']['is_done'] = true;
            }
            
            $item['UserTeacherScene']['modified'] = date('Y-m-d H:i:s');

            if(isset($item['UserTeacherScene']['created'])){ // Update
                $this->UserTeacherScene->id = $id;
                if(!$this->UserTeacherScene->save($item)){
                    $response->error_code = self::ERROR_SYSTEM;
                    $response->message = "Somethings went wrong! Please try again!";
                }
            } else { // Create
                $this->UserTeacherScene->create();
                if(!$this->UserTeacherScene->save($item['UserTeacherScene'])){
                    $response->error_code = self::ERROR_SYSTEM;
                    $response->message = "Somethings went wrong! Please try again!";
                }
            }
            $response->data->is_done = $item['UserTeacherScene']['is_done'] ? '1' : '0';
            return $this->response->body(json_encode($response));
        } else { // Scene detail
            $scene = $this->TeacherScene->find('first', [
                'contain' => ['Media' => [
                        'order' => ['sort_order' => 'DESC']
                    ], 'Feedback'
                ],
                'conditions' => [
                    'TeacherScene.id' => $id
                ],
            ]);
            if(empty($scene)){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "Scene not found!";
                return $this->response->body(json_encode($response));
            }
            $scene['TeacherScene']['is_done'] = $this->_getUserSceneStatus($user['id'], $id);
            
            $scene['TeacherScene']['share_url'] = $this->_generateSceneShareUrl($user['id'], $scene['TeacherScene']['id']);
            $answers = [];
            if(!empty($scene['Feedback'])){
                foreach ($scene['Feedback'] as $feedback) {
                    $answers[] = $feedback['answer'];
                }
            }
            $scene['TeacherScene']['answers'] = $answers;

            $response->data = $scene['TeacherScene'];
            $response->data['feedbacks'] = $scene['Feedback'];
        }
        
        return $this->response->body(json_encode($response));
    }
    
    
    public function api_submissions(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $response->data->submissions = [];
        
        $query = $this->request->query;
        $user_id = '';
        if(!isset($query['user_id'])){
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            $user_id = $user['id'];
        } else {
            $user_id = $query['user_id'];
        }
        
        
        if(!isset($query['limit']) || $query['limit'] <= 0){
            $query['limit'] = 20;
        }
        if(!isset($query['page']) || $query['page'] <= 0){
            $query['page'] = 1;
        }
        
        if(!isset($query['scene_id']) && !isset($query['lesson_id'])){
            $response->error_code = self::ERROR_MISSING_PARAMS;
            $response->message = "scene_id or lesson_id require";
            return $this->response->body(json_encode($response));
        }
        if(isset($query['lesson_id'])){
            $ids = [];
            $scenes = $this->TeacherScene->find('all', [
                'conditions' => ['teacher_lesson_id' => $query['lesson_id']]
            ]);
            if(!empty($scenes)){
                foreach($scenes as $scene){
                    $ids[] = $scene['TeacherScene']['id'];
                }
            }
            
            $items = $this->UserTeacherScene->find('all', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user_id,
                    'teacher_scene_id' => $ids
                ],
            ]);
            $point = 0;
            if(!empty($items)){
                foreach($items as $item){
                    $point += $item['UserTeacherScene']['is_done'] ? 1 : 0;
                    if($item['UserTeacherScene']['answer'] != ''){
                        $submissions = json_decode($item['UserTeacherScene']['answer'], true);
                        if(!empty($submissions)){
                            foreach ($submissions as $sub){
                                if(isset($sub['source']) && $sub['source'] == 'user'){
                                    $response->data->submissions[] = $sub;
                                }
                            }
                        }
                    }
                }
            }
            
            $response->data->scene_finished = $point;
            $response->data->scene_count = count($items);
            
        } else { // scene_id
            $item = $this->UserTeacherScene->find('first', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user_id,
                    'teacher_scene_id' => $query['scene_id']
                ],
            ]);
            if(!empty($item)){
                $response->data->is_done = $item['UserTeacherScene']['is_done'] ? '1' : '0';
                if($item['UserTeacherScene']['answer'] != ''){
                    $submissions = json_decode($item['UserTeacherScene']['answer'], true);
                    if(!empty($submissions)){
                        foreach ($submissions as $sub){
                            if(isset($sub['source']) && $sub['source'] == 'user'){
                                $response->data->submissions[] = $sub;
                            }
                        }
                    }
                }
            }
        }
        
        if(isset($query['sort']) && strtoupper($query['sort']) == 'DESC' && !empty($response->data->submissions)){
            $response->data->submissions = array_reverse($response->data->submissions);
        }
        return $this->response->body(json_encode($response));
    }
    
    private function get_followed_teacher_ids($user_id){
        $cacheKey = "followed_teacher_ids_{$user_id}";
        $res = Cache::read($cacheKey);
        if($res){
            return $res;
        }
        $res = [];
        $items = $this->UserTeacher->find('all', [
            'conditions' => ['user_id' => $user_id]
        ]);
        if(!empty($items)){
            foreach ($items as $item){
                $res[] = $item['UserTeacher']['teacher_id'];
            }
        }
        Cache::write($cacheKey, $res);
        return $res;
    }
    
    
    public function api_lesson_reset(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $id = $this->params['id'];
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        
        if($this->request->is('post')){
            $conditions = [
                    'user_id' => $user['id'],
                    'teacher_lesson_id' => $id
                ];
            $items = $this->UserTeacherScene->find('all', [
                'conditions' => $conditions
            ]);
            if(!empty($items)){
                // Update is_done
                $this->UserTeacherScene->updateAll(['is_done' => 0], $conditions);
                // Update cache
                foreach ($items as $item){
                    $cacheKey = "user_teacher_scene_done_{$user['id']}_{$item['UserTeacherScene']['teacher_scene_id']}";
                    Cache::write($cacheKey, '0', 'persistent');
                }
            }
        } else {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
        }
        
        return $this->response->body(json_encode($response));
    }
    
    
    private function getTeacherLessonLearned($user_id, $teacher_id, $skill_id){
        $res = 0;
        $lesson_ids = $this->TeacherLessonSkill->find('all', [
            'fields' => ['teacher_lesson_id'],
            'conditions' => [
                'teacher_skill_id' => $skill_id
            ]
        ]);
        $ids = [];
        if(empty($lesson_ids)){
            return 0;
        }
        foreach($lesson_ids as $val){
            $ids[] = $val['TeacherLessonSkill']['teacher_lesson_id'];
        }
        $item = $this->RecentView->find('first', [
            'conditions' => ['user_id' => $user_id]
        ]);
        if(empty($item)){
            return 0;
        } else {
            $lessons = json_decode($item['RecentView']['teacher_lessons'], true);
            return count(array_intersect($ids, $lessons));
        }
//        return $this->UserTeacherLesson->find('count', [
//           'conditions' => [
//               'user_id' => $user_id,
//               'teacher_lesson_id' => $ids
//           ]
//        ]);
        
    }
    
    private function getTeacherScenes($user_id, $lesson_id){
        $items = $this->TeacherScene->find('all', [
            'contain' => ['Feedback', 'Media' => [
                'order' => ['sort_order' => 'DESC']
            ]],
            'conditions' => [
                'TeacherScene.teacher_lesson_id' => $lesson_id
            ],
            'order' => [
                'TeacherScene.sort_order' => 'DESC',
                'TeacherScene.id' => 'ASC',
                ]
        ]);
        
        $groups = [];
        if(!empty($items)){
            foreach($items as $scene){
                $group_code = $scene['TeacherScene']['group_code'];
                if($group_code == ""){
                    $group_code = uniqid();
                }
                if(!isset($groups)){
                    $groups[$group_code] = [];
                }
                $medias = [];
                $answers = [];
                if(!empty($scene['Media'])){
                    foreach ($scene['Media'] as $media) {
                        $medias[] = $media;
                    }
                }
                
                if(!empty($scene['Feedback'])){
                    foreach ($scene['Feedback'] as $feedback) {
                        $answers[] = $feedback['answer'];
                    }
                }
                $scene['TeacherScene']['medias'] = $medias;
                $scene['TeacherScene']['answers'] = $answers;
                $scene['TeacherScene']['is_done'] = $this->_getUserSceneStatus($user_id, $scene['TeacherScene']['id']);
                $groups[$group_code][] = $scene['TeacherScene'];
            }
        }
        return array_values($groups);
    }
    
    private function _getUserSceneStatus($user_id, $scene_id){
        $cacheKey = "user_teacher_scene_done_{$user_id}_{$scene_id}";
        $res = Cache::read($cacheKey, 'persistent');
        if($res){
            return $res;
        }
        $item = $this->UserTeacherScene->find('first', [
            'conditions' => [
                'UserTeacherScene.user_id' => $user_id,
                'UserTeacherScene.teacher_scene_id' => $scene_id
            ]
        ]);
        if(!empty($item) && $item['UserTeacherScene']['is_done']){
            Cache::write($cacheKey, '1', 'persistent');
            return '1';
        }
        return '0';
    }
    
    
    private function getFeedbackFromAnswer($scene_id, $answer){
        $items = $this->Feedback->find('all', [
            'conditions' => [
                'Feedback.teacher_scene_id' => $scene_id
            ]
        ]);
        $default = false;
        $answers = explode("|", $answer);
        if(!empty($answers)){
            foreach ($answers as $answer_text){
                if(!empty($items)){
                    foreach($items as $item){
                        if(strtolower(trim($item['Feedback']['answer'])) == strtolower(trim($answer_text))){
                            return $item['Feedback'];
                        } else if ($item['Feedback']['type'] == 'other' && $default == false){
                            $default = $item['Feedback'];
                        }
                    }
                }
            }
        }
        return $default;
    }
}
