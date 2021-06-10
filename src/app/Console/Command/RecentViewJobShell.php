<?php

App::uses('AppShell', 'Console/Command');

class RecentViewJobShell extends AppShell {

    public $uses = array('RecentView');
    public $tasks = ['Gearman.GearmanWorker'];

    public function main() {
        echo date('Y-m-d H:i:s', time()) . "\n";
        $start = microtime(TRUE);
        try{
            $this->GearmanWorker->addFunction('recent_view', $this, 'updateRecentView');
            $this->GearmanWorker->work();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        echo "Time: " . (microtime(TRUE) - $start) . "\n";
    }
    
    public function updateRecentView($data) {
        try{
            if(!isset($data['user_id']) || (!isset($data['lesson_id']) && !isset($data['teacher_lesson_id']))){
                return false;
            }
            $item = $this->RecentView->find('first', [
                'conditions' => [
                    'user_id' => $data['user_id']
                ]
            ]);
            if(empty($item)){ // insert
                $this->RecentView->create();
                $this->RecentView->save([
                    'user_id' => $data['user_id'],
                    'lessons' => isset($data['lesson_id']) ? json_encode([$data['lesson_id']]) : json_encode([]),
                    'teacher_lessons' => isset($data['teacher_lesson_id']) ? json_encode([$data['teacher_lesson_id']]) : json_encode([])
                ]);
            } else { // update lessons
                if(isset($data['lesson_id'])){
                    $lessons = json_decode($item['RecentView']['lessons'], true);
                    if($lessons == null){
                        $lessons = [];
                    } 
                    if (($key = array_search($data['lesson_id'], $lessons)) !== false) {
                        unset($lessons[$key]);
                    }
                    array_unshift($lessons, $data['lesson_id']);

                    $this->RecentView->id = $item['RecentView']['user_id'];
                    $this->RecentView->saveField('lessons', json_encode($lessons));
                } else {
                    $lessons = json_decode($item['RecentView']['teacher_lessons'], true);
                    if($lessons == null){
                        $lessons = [];
                    }
                    if (($key = array_search($data['teacher_lesson_id'], $lessons)) !== false) {
                        unset($lessons[$key]);
                    }
                    array_unshift($lessons, $data['teacher_lesson_id']);

                    $this->RecentView->id = $item['RecentView']['user_id'];
                    $this->RecentView->saveField('teacher_lessons', json_encode($lessons));
                }
            }
            echo date('Y-m-d H:i:s') . ": Updated for {$data['user_id']}\n";
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
}
