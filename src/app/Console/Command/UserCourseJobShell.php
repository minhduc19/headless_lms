<?php

App::uses('AppShell', 'Console/Command');

class UserCourseJobShell extends AppShell {

    public $uses = array('UserCourse');
    public $tasks = ['Gearman.GearmanWorker'];

    public function main() {
        echo date('Y-m-d H:i:s', time()) . "\n";
        $start = microtime(TRUE);
        try{
            $this->GearmanWorker->addFunction('user_courses', $this, 'updateUserCourse');
            $this->GearmanWorker->work();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        echo "Time: " . (microtime(TRUE) - $start) . "\n";
    }
    
    public function updateUserCourse($data) {
        try{
            if(!isset($data['user_id']) || !isset($data['lesson_id']) || !isset($data['course_id'])){
                return false;
            }
            $item = $this->UserCourse->find('first', [
                'conditions' => [
                    'user_id' => $data['user_id'],
                    'course_id' => $data['course_id']
                ]
            ]);
            if(empty($item)){ // insert
                $this->UserCourse->create();
                $this->UserCourse->save([
                    'user_id' => $data['user_id'],
                    'course_id' => $data['course_id'],
                    'lessons' => json_encode([$data['lesson_id']]),
                    'lesson_count' => 1,
                    'last_view' => date('Y-m-d H:i:s')
                ]);
            } else { // update lessons
                $lessons = json_decode($item['UserCourse']['lessons'], true);
                if (($key = array_search($data['lesson_id'], $lessons)) !== false) {
                    unset($lessons[$key]);
                }
                array_unshift($lessons, $data['lesson_id']);
                
                $this->UserCourse->id = $item['UserCourse']['id'];
                $this->UserCourse->save([
                    'lessons' => json_encode($lessons),
                    'lesson_count' => count($lessons),
                    'last_view' => date('Y-m-d H:i:s')
                ]);
            }
            echo date('Y-m-d H:i:s') . ": Updated for {$data['user_id']}\n";
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
}
