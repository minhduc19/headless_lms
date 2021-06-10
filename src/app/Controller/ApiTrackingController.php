<?php

App::uses('AppController', 'Controller');
App::uses('GearmanQueue', 'Gearman.Client');

class ApiTrackingController extends AppController {

    public $uses = array();
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
        
        try{
            if(!$this->request->is('post')){
                $response->error_code = self::ERROR_METHOD_NOT_ALLOW;
                $response->message = "Method not allow!";
                return $this->response->body(json_encode($response));
            }
            $user = $this->getAuthUser();
            if(!$user){
                $response->error_code = self::ERROR_INVALID_TOKEN;
                $response->message = "Invalid access token";
                return $this->response->body(json_encode($response));
            }
            
            $data = $this->request->data;
            $require_fields = ['type', 'payload'];
            foreach ($require_fields as $field) {
                if(!isset($data[$field])){
                    $response->error_code = self::ERROR_MISSING_PARAMS;
                    $response->message = "$field require!";
                    return $this->response->body(json_encode($response));
                }
            }
            
            // Write payload to queue
            GearmanQueue::execute('tracking', [
                'user_id' => $user['id'],
                'type' => $data['type'],
                'payload' => $data['payload'],
            ]);
            
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = "System error!";
        }
               
        return $this->response->body(json_encode($response));
    }
}
