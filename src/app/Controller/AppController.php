<?php

App::uses('Controller', 'Controller');

class AppController extends Controller
{
    public $helpers = array('Breadcrumb', 'Language', 'Html', 'Form', 'Text', 'Filter', 'HtmlExtend');
    public $uses = array('Tag', 'Course', 'Chapter', 'UserLesson', 'Lesson', 'UserTeacher');
    public $auth_user;
    public $components = array('Paginator', 'Session', 'Helper', 'Filter', 'Upload',
        'Auth' => array(
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'Teacher',
                    'fields' => array('username' => 'email'),
                    'scope' => array('status' => 'active'),
                    'passwordHasher' => 'Simple'
                )
            ),
            'loginAction' => array(
                'controller' => 'public',
                'action' => 'login',
                'admin' => true
            ),
            'loginRedirect' => array('controller' => 'public', 'action' => 'login', 'admin' => true),
            'logoutRedirect' => array('controller' => 'public', 'action' => 'login', 'admin' => true),
            'recursive' => 0,
            'authorize' => array('Controller'),
        ),
        'Breadcrumb', 'Sign', 'JWT'
    );
    protected $_arrParam;
    
    const ERROR_SYSTEM = -1;
    const ERROR_INVALID_INPUT = 1;
    const ERROR_MISSING_PARAMS = 2;
    const ERROR_DATA_NOT_FOUND = 3;
    const ERROR_INVALID_PASSWORD = 4;
    const ERROR_INVALID_TOKEN = 5;
    
    const ERROR_INVALID_HASH = 401;
    const ERROR_INVALID_TIMESTAMP = 402;
    const ERROR_METHOD_NOT_ALLOW = 403;
    const ERROR_INVALID_CMD = 404;
    const ALLOW_ENTITY_TYPES = ['course', 'lesson', 'unit'];
   

    public function beforeFilter()
    {
        $this->_arrParam = $this->request->params;
        $this->set('arrParam', $this->_arrParam);
        $this->auth_user = $this->Auth->user();
        if (is_array($this->auth_user) && in_array($this->auth_user['type'], ['admin', 'teacher'])) {
            $this->layout = 'admin/default';
        }else{
            $this->layout = 'default/default';
        }
        $this->set('auth_user', $this->auth_user);
    }

    public function beforeRender()
    {
        if (is_object($this->Breadcrumb)) {
            $this->set('breadcrumbs', $this->Breadcrumb->getBreadcrumbs());
        }
    }

    public function isAuthorized($user)
    {
        return FALSE;
    }
    
    protected function slugify($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        $str = preg_replace("/([%])|(\?)|(:)|(-)|(&)|(–)/", " ", $str);
        $str = preg_replace('!\s+!', ' ', $str);
        $str = str_replace(" ", "-", $str);
        $str = strtolower($str);

        return $str;
    }
    
    protected function getTags(){
        $items = $this->Tag->find('all', ['contain' => false]);
        $tags = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $tags[$item['Tag']['id']] = $item['Tag']['name'];
            }
        }
        $this->set('tags', $tags);
        return $tags;
    }
    
    protected function getCourses(){
        $items = $this->Course->find('all', [
            'contain' => false,
            'order' => ['sort_order' => 'DESC']
            ]);
        $courses = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $courses[$item['Course']['id']] = $item['Course']['title'];
            }
        }
        $this->set('courses', $courses);
        return $courses;
    }
    
    protected function getChapters($course_id = null){
        $conditions = [];
        if($course_id !== null && $course_id != ''){
            $conditions['course_id'] = $course_id;
        }
        $items = $this->Chapter->find('all', [
            'contain' => false,
            'conditions' => $conditions,
            'order' => ['sort_order' => 'DESC', 'id' => 'DESC']
        ]);
        $chapters = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $chapters[$item['Chapter']['id']] = $item['Chapter']['title'];
            }
        }
        $this->set('chapters', $chapters);
        return $chapters;
    }
    
    protected function getLessons($course_id = null){
        $conditions = [];
        if($course_id !== null){
            $conditions['Lesson.course_id'] = $course_id;
        }
        $items = $this->Lesson->find('all', [
            'contain' => false,
            'conditions' => $conditions,
            'order' => ['sort_order' => 'DESC']
            ]);
        $lessons = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $lessons[$item['Lesson']['id']] = $item['Lesson']['title'];
            }
        }
        $this->set('lessons', $lessons);
        return $lessons;
    }
    
    protected function getUnits($lesson_id = null){
        $conditions = [];
        if($lesson_id !== null){
            $conditions['Unit.lesson_id'] = $lesson_id;
        }
        $items = $this->Unit->find('all', [
            'contain' => false,
            'conditions' => $conditions,
            'order' => ['sort_order' => 'DESC']
            ]);
        $units = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $units[$item['Unit']['id']] = $item['Unit']['title'];
            }
        }
        $this->set('units', $units);
        return $units;
    }
    
    protected function getAccessToken() {
        $auth = $this->request->header('Authorization');
        if (!$auth && function_exists('apache_request_headers')) {
            $apache_headers = apache_request_headers();
            $auth = isset($apache_headers['Authorization']) ? $apache_headers['Authorization'] : '';
        }
        $token = explode(" ", $auth);
        if (count($token) == 2) {
            return $token[1];
        }
        return false;
    }
    
    protected function getAuthUser(){
        //return ['id' => '0034c3d0-ac18-4d7b-b982-d759850ae0e1'];
        $token = $this->getAccessToken();
        if($token){
            $cache_key = sha1($token);
            $user = Cache::read($cache_key, 'week');
            if(!$user){
                $user = $this->JWT->getUserFromAccessToken($token);
                Cache::write($cache_key, $user, 'week');
            }
            return $user;
        }
        return false;
    }
    
    protected function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
    
    protected function validateHash($params){
        if($params['ts'] == $params['hash']){
            return true;
        }
        try{
            // Validate timestamp
            $response = new stdClass();
            $current = time();
            if($params['ts'] < $current - 60 || $params['ts'] > $current + 60){
                $response->error_code = self::ERROR_INVALID_TIMESTAMP;
                $response->message = "Invalid timestamp, server timestamp = $current, sent timestamp = " . $params['ts'];
                return $response;
            }

            // Validate hash
            $hash = $params['hash'];
            unset($params['hash']);
            if(!$this->Sign->verify($params, $hash)){
                $response->error_code = self::ERROR_INVALID_HASH;
                $response->message = "Invalid hash!";
                return $response;
            }

            return true;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $response->error_code = self::ERROR_SYSTEM;
            $response->message = $ex->getMessage();
            return $response;
        }
    }
    
    protected function _getTagObjects($tags){
        $result = [];
        if(!empty($tags)){
            foreach ($tags as $tag) {
                if(!empty($tag['Tag'])){
                    $result[] = [
                        'id' => $tag['Tag']['id'],
                        'name' => $tag['Tag']['name'],
                    ];
                }
            }
        }
        return $result;
    }
    
    protected function _getLessionObjects($lessons){
        $result = [];
        if(!empty($lessons)){
            foreach ($lessons as $lesson) {
                $lesson_row = [
                    'id' => $lesson['id'],
                    'course_id' => $lesson['course_id'],
                    'thumb' => $lesson['thumb'],
                    'title' => $lesson['title'],
                    'short_description' => $lesson['short_description'],
                    'total_unit' => $lesson['total_unit'],
                    'tags' => $this->_getTagObjects($lesson['LessonTag'])
                ];
                $result[] = $lesson_row;
            }
        }
        return $result;
    }
    
    protected function _checkViewedUnit($user_id, $lesson_id, $unit_id){
        $cacheKey = "viewed_{$user_id}_{$lesson_id}_{$unit_id}";
        $res = Cache::read($cacheKey, 'persistent');
        if($res){
            return $res;
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
        }
        $units = json_decode($item['UserLesson']['units'], true);
        if(in_array($unit_id, $units)){
            Cache::write($cacheKey, true, 'persistent');
            return true;
        }
        return false;
    }
    
    protected function getOS($user_agent) { 

        $os_platform  = "Unknown OS Platform";

        $os_array     = array(
                              '/windows nt 10/i'      =>  'Windows 10',
                              '/windows nt 6.3/i'     =>  'Windows 8.1',
                              '/windows nt 6.2/i'     =>  'Windows 8',
                              '/windows nt 6.1/i'     =>  'Windows 7',
                              '/windows nt 6.0/i'     =>  'Windows Vista',
                              '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                              '/windows nt 5.1/i'     =>  'Windows XP',
                              '/windows xp/i'         =>  'Windows XP',
                              '/windows nt 5.0/i'     =>  'Windows 2000',
                              '/windows me/i'         =>  'Windows ME',
                              '/win98/i'              =>  'Windows 98',
                              '/win95/i'              =>  'Windows 95',
                              '/win16/i'              =>  'Windows 3.11',
                              '/macintosh|mac os x/i' =>  'Mac OS X',
                              '/mac_powerpc/i'        =>  'Mac OS 9',
                              '/linux/i'              =>  'Linux',
                              '/ubuntu/i'             =>  'Ubuntu',
                              '/iphone/i'             =>  'iPhone',
                              '/ipod/i'               =>  'iPod',
                              '/ipad/i'               =>  'iPad',
                              '/android/i'            =>  'Android',
                              '/blackberry/i'         =>  'BlackBerry',
                              '/webos/i'              =>  'Mobile'
                        );

        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;

        return $os_platform;
    }
    
    protected function isFollowed($user_id, $teacher_id){
        $cacheKey = "follow_{$user_id}_{$teacher_id}";
//        $res = Cache::read($cacheKey);
//        if($res){
//            return $res;
//        }
        $item = $this->UserTeacher->findByUserIdAndTeacherId($user_id, $teacher_id);
        if(!empty($item)){
            $res = 1;
            Cache::write($cacheKey, $res);
            return $res;
        }
        return 0;
    }
    
    protected function _generateSceneShareUrl($user_id, $scene_id){
        return Router::url('/', true) . 'scene/' . $scene_id . "/{$user_id}";
    }
    
    protected function _generateLessonShareUrl($user_id, $lesson_id){
        return Router::url('/', true) . 'teacher_lesson/' . $lesson_id . "/{$user_id}";
    }
    
    protected function _generateTeacherShareUrl($teacher_id){
        return Router::url('/', true) . 't/' . $teacher_id;
    }
}
