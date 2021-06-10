<?php

App::uses('AppController', 'Controller');
App::uses('GearmanQueue', 'Gearman.Client');

class ApiLessonsController extends AppController {

    public $uses = array('Lesson', 'Tag', 'LessonTag', 'Unit', 
        'UserFavorite', 'RecentView', 'UserLesson');
    public $components = array();

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
        $item = $this->Lesson->find('first', [
            'contain' => ['Unit', 'LessonTag' => 'Tag'],
            'conditions' => [
                'Lesson.id' => $id
            ]
        ]);
        $user = $this->getAuthUser();
        if (!empty($item)) {
            $response->data->id = $item['Lesson']['id'];
            $response->data->course_id = $item['Lesson']['course_id'];
            $response->data->chapter_id = $item['Lesson']['chapter_id'];
            $response->data->title = $item['Lesson']['title'];
            $response->data->thumb = $item['Lesson']['thumb'];
            $response->data->favorite_count = $item['Lesson']['favorite_count'];
            $response->data->short_description = $item['Lesson']['short_description'];
            $response->data->total_unit = $item['Lesson']['total_unit'];
            $response->data->modified = $item['Lesson']['modified'];

            $response->data->share_url = Router::url('/', true) . 'lesson/' . $id;
            $share_image = "https://chart.googleapis.com/chart?cht=qr&chs=250&chl=". urlencode($response->data->share_url);
            $response->data->share_image = $share_image;
            $response->data->tags = $this->_getTagObjects($item['LessonTag']);
            $response->data->units = [];
            if (!empty($item['Unit'])) {
                foreach ($item['Unit'] as $unit) {
                    if(empty($unit)){
                        continue;
                    }
                    if(!$user){
                        $unit['viewed'] = false;
                    } else {
                        $unit['viewed'] = $this->_checkViewedUnit($user['id'], $id, $unit['id']);
                    }
                    $response->data->units[] = $unit;
                }
            }
            $response->data->viewed_unit = (string)count($response->data->units);
            if($user){
                $response->data->is_favorite = $this->__isFavorite($user, $item['Lesson']['id']);
                // Log for recent view
                GearmanQueue::execute('recent_view', [
                    'user_id' => $user['id'],
                    'lesson_id' => $id,
                ]);
                // Log for user courses
                GearmanQueue::execute('user_courses', [
                    'user_id' => $user['id'],
                    'course_id' => $item['Lesson']['course_id'],
                    'lesson_id' => $id,
                ]);
            } else {
                $response->data->is_favorite = false;
            }
        }
        return $this->response->body(json_encode($response));
    }
    
    
    public function api_recents() {
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
            $item = $this->RecentView->find('first', [
                'conditions' => ['user_id' => $user['id']]
            ]);
            if(empty($item)){
                return $this->response->body(json_encode($response));
            }
            
            $recents = json_decode($item['RecentView']['lessons'], true);
            
            $query = $this->request->query;
            if(!isset($query['limit']) || $query['limit'] <= 0){
                $query['limit'] = 20;
            } 
            if(!isset($query['page']) || $query['page'] <= 0){
                $query['page'] = 1;
            }
            $items = $this->Lesson->find('all', [
                'contain' => ['LessonTag' => 'Tag'],
                'conditions' => [
                    'Lesson.id' => $recents,
                ],
                'order' => 'FIELD(Lesson.id, ' . implode(",", $recents) . ')',
                'limit' => $query['limit'],
                'page' => $query['page']
            ]);
            
            if(!empty($items)){
                foreach($items as $lesson){
                    $user_lesson = $this->__getViewedLesson($user['id'], $lesson['Lesson']['id']);
                    $lastest_unit = new stdClass();
                    if($user_lesson){
                        $units = json_decode($user_lesson['units'], true);
                        $unit_id = $units[0];
                        $unit = $this->Unit->findById($unit_id);
                        if(!empty($unit)){
                            $lastest_unit = $unit['Unit'];
                        }
                    }
                    $row = [
                        'id' => $lesson['Lesson']['id'],
                        'course_id' => $lesson['Lesson']['course_id'],
                        'chapter_id' => $lesson['Lesson']['chapter_id'],
                        'title' => $lesson['Lesson']['title'],
                        'thumb' => $lesson['Lesson']['thumb'],
                        'short_description' => $lesson['Lesson']['short_description'],
                        'total_unit' => $lesson['Lesson']['total_unit'],
                        'viewed_unit' => ($user_lesson != false) ? $user_lesson['unit_count'] : 0,
                        'lastest_unit' => $lastest_unit,
                        'tags' => $this->_getTagObjects($lesson['LessonTag'])
                    ];
                    $response->data[] = $row;
                }
            }
            
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }
        return $this->response->body(json_encode($response));
    }
    
    public function api_new_releases() {
        $response = new stdClass();
        $response->error_code = 0;
        $response->message = "Success";
        $response->data = [];
        try{
            $query = $this->request->query;
            if(!isset($query['limit']) || $query['limit'] <= 0){
                $query['limit'] = 20;
            } 
            if(!isset($query['page']) || $query['page'] <= 0){
                $query['page'] = 1;
            }
            $cacheKey = "new_release_lessons_{$query['page']}_{$query['limit']}";
            $data = Cache::read($cacheKey, 'short');
            if($data){
                $response->data = $data;
                return $this->response->body(json_encode($response));
            }
            $items = $this->Lesson->find('all', [
                'contain' => ['LessonTag' => 'Tag'],
                'order' => ['Lesson.id' => 'DESC'],
                'limit' => $query['limit'],
                'page' => $query['page']
            ]);
            
            if(!empty($items)){
                foreach($items as $lesson){
                    $row = [
                        'id' => $lesson['Lesson']['id'],
                        'course_id' => $lesson['Lesson']['course_id'],
                        'chapter_id' => $lesson['Lesson']['chapter_id'],
                        'title' => $lesson['Lesson']['title'],
                        'thumb' => $lesson['Lesson']['thumb'],
                        'short_description' => $lesson['Lesson']['short_description'],
                        'total_unit' => $lesson['Lesson']['total_unit'],
                        'tags' => $this->_getTagObjects($lesson['LessonTag'])
                    ];
                    $response->data[] = $row;
                }
            }
            Cache::write($cacheKey, $response->data, 'short');
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
        }
        return $this->response->body(json_encode($response));
    }


    private function __isFavorite($user, $lesson_id){
        $item = $this->UserFavorite->find('first', [
            'conditions' => [
                'user_id' => $user['id'],
                'entity_id' => $lesson_id,
                'entity_type' => 'lesson'
            ]
        ]);
        if(!empty($item)){
            return true;
        }
        return false;
    }
    
    private function __getViewedLesson($user_id, $lesson_id){
        $cacheKey = "viewed_lesson_{$user_id}_{$lesson_id}";
        $res = Cache::read($cacheKey, 'persistent');
        if($res){
            //return $res;
        }
        
        $item = $this->UserLesson->find('first', [
            'contain' => false,
            'conditions' => [
                'user_id' => $user_id,
                'lesson_id' => $lesson_id
            ]
        ]);
        
        if(empty($item)){
            return false;
        } else {
            Cache::write($cacheKey, $item['UserLesson'], 'persistent');
        }
        return $item['UserLesson'];
    }

}
