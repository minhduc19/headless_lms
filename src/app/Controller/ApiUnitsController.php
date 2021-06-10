<?php

App::uses('AppController', 'Controller');
//App::uses('GearmanQueue', 'Gearman.Client');

class ApiUnitsController extends AppController {

    public $uses = array('Unit', 'Scene', 'Media', 'UserScene', 'Comment', 'Feedback');
    public $components = array('Google');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $this->autoRender = false;
        $this->response->type('json');
    }

    public function api_detail() {
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
        $item = $this->Unit->findById($id);
        if(empty($item)){
            $response->error_code = self::ERROR_DATA_NOT_FOUND;
            $response->message = "Unit not found!";
            return $this->response->body(json_encode($response));
        }
        $response->data = json_decode(json_encode($item['Unit']));
        $response->data->scenes = $this->_getScenes($user['id'], $item['Unit']['id']);
        
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

        if($this->request->is('post')){
            $scene = $this->Scene->findById($id);
            if(empty($scene)){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "Scene not found!";
                return $this->response->body(json_encode($response));
            }
            // Check Group
//            if($scene['Scene']['group_code'] != ""){
//                // Get scenes bigger sort_order
//                $scenes = $this->Scene->find('all', [
//                    'contain' => false,
//                    'conditions' => [
//                        'id != ' => $id,
//                        'unit_id' => $scene['Scene']['unit_id'],
//                        'group_code' => $scene['Scene']['group_code'],
//                        'sort_order > ' => $scene['Scene']['sort_order']
//                    ]
//                ]);
//                if(!empty($scenes)){
//                    foreach ($scenes as $s) {
//                        if(!$this->_getUserSceneStatus($user['id'], $s['Scene']['id'])){
//                            $response->error_code = self::ERROR_INVALID_INPUT;
//                            $response->message = "You need finish previous scenes to continue!";
//                            return $this->response->body(json_encode($response));
//                        }   
//                    }
//                }
//            }
            
            $item = $this->UserScene->find('first', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user['id'],
                    'scene_id' => $id
                ],
            ]);
            if(empty($item)){
                $item = ['UserScene' => [
                    'user_id' => $user['id'],
                    'unit_id' => $scene['Scene']['unit_id'],
                    'scene_id' => $id,
                    'answer' => '',
                    'is_done' => false
                ]];
            }
            $data = $this->request->data;
            if($scene['Scene']['type'] != 'view'){
                if(!isset($data['type']) || !in_array($data['type'], ['text', 'audio']) || !isset($data['value']) || $data['value'] == ""){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "Missing type or value!";
                    return $this->response->body(json_encode($response));
                }
            }
            
            if(isset($data['type']) && isset($data['value']) && $data['value'] != ""){
                $answer = [];
                if($item['UserScene']['answer'] != ''){
                    $answer = json_decode($item['UserScene']['answer'], true);
                }
                if(!$answer){
                    $answer = [];
                }
                $answer[] = [
                    'source' => 'user',
                    'type' => $data['type'],
                    'value' => $data['value'],
                    'created' => time()
                ];
                
                // Feedback
                $answer_text = $data['value'];
                if($data['type'] == 'audio'){
                    if(strpos($data['value'], 'http') !== 0){
                        $response->error_code = self::ERROR_INVALID_INPUT;
                        $response->message = "Invalid audio url!";
                        return $this->response->body(json_encode($response));
                    }
                    $item['UserScene']['is_done'] = true; // auto done
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
                        $item['UserScene']['is_done'] = true;
                    }
                    $response->data->feedback_type = $feedback['type'];
                    $response->data->feedback = $feedback['feedback'];
                }
                
                $item['UserScene']['answer'] = json_encode($answer);
            }
            
            if(in_array($scene['Scene']['type'], ['view', 'submission'])){
                $item['UserScene']['is_done'] = true;
            }
            
            $item['UserScene']['modified'] = date('Y-m-d H:i:s');

            if(isset($item['UserScene']['created'])){ // Update
                $this->UserScene->id = $id;
                if(!$this->UserScene->save($item)){
                    $response->error_code = self::ERROR_SYSTEM;
                    $response->message = "Somethings went wrong! Please try again!";
                }
            } else { // Create
                $this->UserScene->create();
                if(!$this->UserScene->save($item['UserScene'])){
                    $response->error_code = self::ERROR_SYSTEM;
                    $response->message = "Somethings went wrong! Please try again!";
                }
            }
            $response->data->is_done = $item['UserScene']['is_done'] ? '1' : '0';
            return $this->response->body(json_encode($response));
        } else {
            
            $scene = $this->Scene->find('first', [
                'contain' => ['Media' => [
                        'order' => ['sort_order' => 'DESC']
                    ], 'Feedback'
                ],
                'conditions' => [
                    'Scene.id' => $id
                ],
            ]);
            $scene['Scene']['is_done'] = $this->_getUserSceneStatus($user['id'], $id);
            
            $scene['Scene']['medias'] = $scene['Media'];

            $response->data = $scene['Scene'];
            $response->data['feedbacks'] = $scene['Feedback'];
        }
        
        return $this->response->body(json_encode($response));
    }
    
    public function api_comments(){
        $response = new stdClass();
        $id = $this->params['id'];
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        
        if($this->request->is('post')){
            $data = $this->request->data;
            if((!isset($data['content']) || $data['content'] == "") && (!isset($data['medias']) || $data['medias'] == '')){
                $response->error_code = self::ERROR_MISSING_PARAMS;
                $response->message = "Missing content or medias!";
                return $this->response->body(json_encode($response));
            }
            $scene = $this->Scene->findById($id);
            if(!$scene){
                $response->error_code = self::ERROR_DATA_NOT_FOUND;
                $response->message = "Invalid scene_id";
                return $this->response->body(json_encode($response));
            }
            $this->Comment->create();
            $this->Comment->save([
                'user_id' => $user['id'],
                'unit_id' => $scene['Scene']['unit_id'],
                'scene_id' => $id,
                'content' => $data['content']
            ]);
            
            // Save medias
            if(isset($data['medias'])){
                $medias = json_decode($data['medias'], true);
                if($medias){
                    $arrMedias = [];
                    foreach ($medias as $media) {
                        if(isset($media['type']) && isset($media['value'])){
                            $arrMedias[] = ['Media' => [
                                's3_key' => isset($media['s3_key']) ? $media['s3_key'] : '',
                                'type' => $media['type'],
                                'url' => $media['value'],
                                'comment_id' => $this->Comment->getLastInsertId()
                            ]];
                        }
                    }
                    if(!empty($arrMedias)){
                        $this->Media->saveAll($arrMedias);
                    }
                }
            }
            
        }
        
        return $this->response->body(json_encode($response));
    }

    public function api_reset(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $unit_id = $this->params['id'];
        $user = $this->getAuthUser();
        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        
        if($this->request->is('post')){
            $conditions = [
                    'user_id' => $user['id'],
                    'unit_id' => $unit_id
                ];
            $items = $this->UserScene->find('all', [
                'conditions' => $conditions
            ]);
            if(!empty($items)){
                // Update is_done
                $this->UserScene->updateAll(['is_done' => 0], $conditions);
                // Update cache
                foreach ($items as $item){
                    $cacheKey = "user_scene_done_{$user['id']}_{$item['UserScene']['scene_id']}";
                    Cache::write($cacheKey, '0', 'persistent');
                }
            }
        } else {
            $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
            $response->message = "Method not allow";
        }
        
        return $this->response->body(json_encode($response));
    }

    private function _getScenes($user_id, $unit_id){
        $items = $this->Scene->find('all', [
            'contain' => ['Media' => [
                'order' => ['sort_order' => 'DESC']
            ]],
            'conditions' => [
                'Scene.unit_id' => $unit_id
            ],
            'order' => ['Scene.sort_order' => 'DESC']
        ]);
        
        $groups = [];
        if(!empty($items)){
            foreach($items as $scene){
                $group_code = $scene['Scene']['group_code'];
                if($group_code == ""){
                    $group_code = uniqid();
                }
                if(!isset($groups)){
                    $groups[$group_code] = [];
                }
                $medias = [];
                if(!empty($scene['Media'])){
                    foreach ($scene['Media'] as $media) {
                        $medias[] = $media;
                    }
                }
                $scene['Scene']['medias'] = $medias;
                $scene['Scene']['is_done'] = $this->_getUserSceneStatus($user_id, $scene['Scene']['id']);
                $groups[$group_code][] = $scene['Scene'];
            }
        }
        return array_values($groups);
    }

    private function _getUserSceneStatus($user_id, $scene_id){
        $cacheKey = "user_scene_done_{$user_id}_{$scene_id}";
        $res = Cache::read($cacheKey, 'persistent');
        if($res){
            return $res;
        }
        $item = $this->UserScene->find('first', [
            'conditions' => [
                'UserScene.user_id' => $user_id,
                'UserScene.scene_id' => $scene_id
            ]
        ]);
        if(!empty($item) && $item['UserScene']['is_done']){
            Cache::write($cacheKey, '1', 'persistent');
            return '1';
        }
        return '0';
    }
    
    private function getFeedbackFromAnswer($scene_id, $answer){
        $items = $this->Feedback->find('all', [
            'conditions' => [
                'Feedback.scene_id' => $scene_id
            ]
        ]);
        $default = false;
        if(!empty($items)){
            foreach($items as $item){
                if(strtolower(trim($item['Feedback']['answer'])) == strtolower(trim($answer))){
                    return $item['Feedback'];
                } else if ($item['Feedback']['type'] == 'other' && $default == false){
                    $default = $item['Feedback'];
                }
            }
        }
        return $default;
    }
}
