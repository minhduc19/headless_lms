<?php

App::uses('AppController', 'Controller');

class ApiCommentsController extends AppController {

    public $uses = array('Comment');
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
        $response->data = [];
        
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
        $conditions = [];
        if(isset($query['scene_id']) && $query['scene_id']){
            $conditions += ['Comment.scene_id' => $query['scene_id']];
        } else if (isset($query['unit_id']) && $query['unit_id']){
            $conditions += ['Comment.unit_id' => $query['unit_id']];
        }
        
        $items = $this->Comment->find('all', [
            'contain' => ['Media', 'User'],
            'conditions' => $conditions,
            'limit' => $query['limit'],
            'page' => $query['page'],
            'order' => ['Comment.id' => 'DESC']
        ]);

        
        if(!empty($items)){
            foreach ($items as $comment) {
                $row = [
                    'id' => $comment['Comment']['id'],
                    'content' => $comment['Comment']['content'],
                    'created' => $comment['Comment']['created'],
                    'user_id' => $comment['Comment']['user_id'],
                    'first_name' => $comment['User']['first_name'],
                    'last_name' => $comment['User']['last_name'],
                    'avatar' => $comment['User']['avatar'],
                    'medias' => []
                ];
                if(!empty($comment['Media'])){
                    foreach ($comment['Media'] as $media) {
                        $row['medias'][] = $media;
                    }
                }

                $response->data[] = $row;
            }
        }
        
        return $this->response->body(json_encode($response));
    }
}
