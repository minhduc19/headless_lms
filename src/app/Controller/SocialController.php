<?php

App::uses('AppController', 'Controller');
require_once APP . 'vendor' . DS . "autoload.php";

class SocialController extends AppController {

    public $uses = array('Admin', 'Teacher');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    public function isAuthorized($user) {
        return TRUE;
    }

    public function google_oauth() {
        if ($this->Auth->loggedIn() && $this->auth_user['type'] == 'admin') {
            $this->redirect(array('controller' => 'courses', 'action' => 'index', 'admin' => true));
        }
        $this->autoRender = false;
        $redirectUri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/social/google_oauth';
        $client = new Google_Client();
        $client->setApplicationName(Configure::read('GOOGLE_APP_NAME'));
        $client->setClientId(Configure::read('GOOGLE_OAUTH_CLIENT_ID'));
        $client->setClientSecret(Configure::read('GOOGLE_OAUTH_CLIENT_SECRET'));
        $client->setRedirectUri($redirectUri);
        $client->setScopes(array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ));
        $query = $this->request->query;
        if (!isset($query['code'])) {
            $url = $client->createAuthUrl();
            $this->redirect($url);
        } else {
            $client->authenticate($query['code']); // Authenticate
            $oauth2 = new Google_Service_Oauth2($client);
            $user = $oauth2->userinfo->get();
            try {
                $result = $this->Admin->findByEmail($user['email']);
                if (!empty($result)) {
                    $data = $result['Admin'];
                    $data['email'] = $user['email'];
                    $data['name'] = $user['name'];
                    $data['social_id'] = $user['id'];
                    $data['avatar'] = $user['picture'];
                    $this->Admin->id = $result['Admin']['id'];
                    if ($this->Admin->save($data)) {
                        if($result['Admin']['status'] != 'active'){
                            $this->Session->setFlash("Account has been locked!", 'flash-error');
                            return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => true));
                        }
                        $data['id'] = $result['Admin']['id'];
                        $data['type'] = $result['Admin']['type'];
                        if ($this->Auth->login($data)) {
                            $this->redirect(array('controller' => 'courses', 'action' => 'index', 'admin' => true));
                        } else {
                            $this->Session->setFlash("Failed to login Google", 'flash-error');
                            return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => true));
                        }
                    } else {
                        $this->Session->setFlash("Failed to login Google", 'flash-error');
                        return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => true));
                    }
                } else {
                    $this->Session->setFlash("Access denied!", 'flash-error');
                    return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => true));
                }
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'flash-error');
                return $this->redirect(array('controller' => 'public', 'action' => 'login', 'admin' => true));
            }
        }
    }
    
    public function teacher_google_oauth() {
        if ($this->Auth->loggedIn() && $this->auth_user['type'] == 'teacher') {
            $this->redirect(array('controller' => 'lessons', 'action' => 'index', 'teacher' => true));
        }
        $this->autoRender = false;
        $redirectUri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/teacher/social/google_oauth';
        $client = new Google_Client();
        $client->setApplicationName(Configure::read('GOOGLE_APP_NAME'));
        $client->setClientId(Configure::read('GOOGLE_OAUTH_CLIENT_ID'));
        $client->setClientSecret(Configure::read('GOOGLE_OAUTH_CLIENT_SECRET'));
        $client->setRedirectUri($redirectUri);
        $client->setScopes(array(
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ));
        $query = $this->request->query;
        if (!isset($query['code'])) {
            $url = $client->createAuthUrl();
            $this->redirect($url);
        } else {
            $client->authenticate($query['code']); // Authenticate
            $oauth2 = new Google_Service_Oauth2($client);
            $user = $oauth2->userinfo->get();
            try {
                $result = $this->Teacher->findByEmail($user['email']);
                if (!empty($result)) {
                    $data = $result['Teacher'];
                    $data['email'] = $user['email'];
                    $data['name'] = $user['name'];
                    $data['social_id'] = $user['id'];
                    $data['avatar'] = $user['picture'];
                    $this->Teacher->id = $result['Teacher']['id'];
                    if ($this->Teacher->save($data)) {
                        if($result['Teacher']['status'] != 'active'){
                            $this->Session->setFlash("Account has been locked!", 'flash-error');
                            return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => true));
                        }
                        $data['id'] = $result['Teacher']['id'];
                        $data['type'] = 'teacher';
                        if ($this->Auth->login($data)) {
                            $this->redirect(array('controller' => 'lessons', 'action' => 'index', 'teacher' => true));
                        } else {
                            $this->Session->setFlash("Failed to login Google", 'flash-error');
                            return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => true));
                        }
                    } else {
                        $this->Session->setFlash("Failed to login Google", 'flash-error');
                        return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => true));
                    }
                } else {
                    $this->Session->setFlash("Access denied!", 'flash-error');
                    return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => true));
                }
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'flash-error');
                return $this->redirect(array('controller' => 'public', 'action' => 'login', 'teacher' => true));
            }
        }
    }

}
