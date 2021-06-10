<?php

App::uses('AppShell', 'Console/Command');

class TrackingJobShell extends AppShell {

    public $uses = array('Unit', 'UserLesson');
    public $tasks = ['Gearman.GearmanWorker'];

    public function main() {
        echo date('Y-m-d H:i:s', time()) . "\n";
        $start = microtime(TRUE);
        try{
            $this->GearmanWorker->addFunction('tracking', $this, 'processTracking');
            $this->GearmanWorker->work();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        echo "Time: " . (microtime(TRUE) - $start) . "\n";
    }
    
    public function processTracking($data) {
        try{
            if(!isset($data['user_id']) || !isset($data['type']) || !isset($data['payload'])){
                echo "Missing params. data = " . json_encode($data) . "\n";
                return false;
            }
            $res = false;
            switch ($data['type']){
                case 'view_unit':
                    $res = $this->trackViewUnit($data['user_id'], $data['payload']);
                    break;
                default:
                    break;
            }
            if($res){
                echo date('Y-m-d H:i:s') . ": Updated for {$data['user_id']}\n";
            }
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
    
    private function trackViewUnit($user_id, $payload){
        if(is_string($payload)){
            $payload = json_decode($payload, true);
        }
        if(is_array($payload) && isset($payload['unit_id'])){
            $item = $this->Unit->findById($payload['unit_id']);
            if(empty($item)){
                echo "Unit not exists {$payload['unit_id']}\n";
                return false;
            }
            if(empty($item['Lesson'])){
                echo "Lesson not exists {$item['Unit']['lesson_id']}\n";
                return false;
            }
            $log = $this->UserLesson->find('first', [
                'conditions' => [
                    'user_id' => $user_id,
                    'lesson_id' => $item['Unit']['lesson_id']
                ]
            ]);
            $lesson_id = $item['Unit']['lesson_id'];
            $cacheKey = "viewed_lesson_{$user_id}_{$lesson_id}";
            if(empty($log)){ // insert
                $item = [
                    'user_id' => $user_id,
                    'lesson_id' => $item['Unit']['lesson_id'],
                    'units' => json_encode([$payload['unit_id']]),
                    'unit_count' => 1,
                    'last_view' => date('Y-m-d H:i:s')
                ];
                $this->UserLesson->create();
                $this->UserLesson->save($item);
            } else { // update units
                $units = json_decode($log['UserLesson']['units'], true);
                if (($key = array_search($payload['unit_id'], $units)) !== false) {
                    unset($units[$key]);
                }
                array_unshift($units, $payload['unit_id']);
                $item = [
                    'units' => json_encode($units),
                    'unit_count' => count($units),
                    'last_view' => date('Y-m-d H:i:s')
                ];
                $this->UserLesson->id = $log['UserLesson']['id'];
                $this->UserLesson->save($item);
            }
            Cache::write($cacheKey, $item, 'persistent');
            return true;
        } else {
            echo "Missing unit_id on payload!\n";
            return false;
        }
    }
}
