<?php

App::uses('AppController', 'Controller');

class ApiSubmissionsController extends AppController {

    public $uses = array('UserScene');
    public $components = array();

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
        $this->autoRender = false;
        $this->response->type('json');
    }

    public function api_index(){
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = new stdClass();
        $response->data->submissions = [];
        
        $user = $this->getAuthUser();

        if(!$user){
            $response->error_code = self::ERROR_INVALID_TOKEN;
            $response->message = "Invalid access token";
            return $this->response->body(json_encode($response));
        }
        
        $query = $this->request->query;
        if(!isset($query['limit']) || $query['limit'] <= 0){
            $query['limit'] = 20;
        }
        if(!isset($query['page']) || $query['page'] <= 0){
            $query['page'] = 1;
        }

        if(isset($query['scene_id']) && $query['scene_id']){
            $item = $this->UserScene->find('first', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user['id'],
                    'scene_id' => $query['scene_id']
                ],
            ]);
            if(!empty($item)){
                $response->data->is_done = $item['UserScene']['is_done'] ? '1' : '0';
                if($item['UserScene']['answer'] != ''){
                    $submissions = json_decode($item['UserScene']['answer'], true);
                    if(!empty($submissions)){
                        foreach ($submissions as $sub){
                            if(isset($sub['source']) && $sub['source'] == 'user'){
                                $response->data->submissions[] = $sub;
                            }
                        }
                    }
                }
            }
        } else if (isset($query['unit_id']) && $query['unit_id']){
            $items = $this->UserScene->find('all', [
                'contain' => false, 
                'conditions' => [
                    'user_id' => $user['id'],
                    'unit_id' => $query['unit_id']
                ],
            ]);
            $point = 0; 
            if(!empty($items)){
                foreach ($items as $item){
                    $point += $item['UserScene']['is_done'] ? 1 : 0;
                    if($item['UserScene']['answer'] != ''){
                        $submissions = json_decode($item['UserScene']['answer'], true);
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
        }
        
        if(isset($query['sort']) && strtoupper($query['sort']) == 'DESC' && !empty($response->data->submissions)){
            $response->data->submissions = array_reverse($response->data->submissions);
        }
        return $this->response->body(json_encode($response));
    }
}
