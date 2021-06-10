<?php

App::uses('AppController', 'Controller');

class ProfileController extends AppController
{

    public $uses = array('Admin', 'Teacher');
    public $components = array('CRUD', 'S3');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function isAuthorized($user)
    {
        if (in_array($user['type'], ['admin', 'teacher'])) {
            return true;
        }
        return false;
    }

    public function admin_index()
    {
        $this->set('title', __('Update profile'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Update profile'), 'slug' => '#'));
        $this->set('title_for_layout', __('Update profile'));
        if ($this->request->is(array('post', 'put'))) {
            if($this->auth_user['type'] == 'teacher'){
                $teacher = $this->Teacher->findById($this->auth_user['id']);
                $data = $this->request->data;
                $this->Teacher->id = $this->auth_user['id'];
//                $skills = explode(",", $data['User']['skills']);
//                $old_skills = json_decode($teacher['Teacher']['skills'], true);
//                $new_skills = [];
//                if(!empty($skills)){
//                    foreach ($skills as $skill) {
//                        if(isset($old_skills[$skill])){
//                            $new_skills[$skill] = $old_skills[$skill];
//                        } else {
//                            $new_skills[$skill] = 0;
//                        }
//                    }
//                }
//                $data['User']['skills'] = json_encode($new_skills);
//                $this->auth_user['skills'] = json_encode($new_skills);
                
                list($s3_key, $avatar) = $this->S3->upload($this->request->data['Image']['avatar'], array('type' => 'avatar'));
                if ($avatar) {
                    $data['User']['avatar'] = $avatar;
                    $this->auth_user['avatar'] = $avatar;
                }
                
                if ($this->Teacher->save($data['User'])) {
                    $this->Auth->login($this->auth_user);
                    $this->Session->setFlash(__('Profile has been updated'), 'flash-success', array());
                    return $this->redirect(array('action' => 'index', 'admin' => true));
                }
                $this->Session->setFlash(__('Profile could not be saved. Please, try again.'), 'flash-error', array());
            }
        }
    }

    public function admin_edit()
    {
        $this->set('title', __('Edit profile'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit profile'), 'slug' => '#'));
        $this->set('title_for_layout', __('Edit profile'));
        if ($this->request->is(array('post', 'put'))) {
            $data = $this->request->data;
            $this->Admin->id = $data['User']['id'];
            if ($this->Admin->save($data['User'])) {
                $this->_reload($this->request->data['User']);
                $this->Session->setFlash(__('Profile has been updated'), 'flash-success', array());
                return $this->redirect(array('action' => 'index', 'admin' => true));
            }
            $this->Session->setFlash(__('Profile could not be saved. Please, try again.'), 'flash-error', array());
        }
    }

    public function admin_change_password()
    {
        $this->set('title', __('Change password'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Change password'), 'slug' => '#'));
        if ($this->request->is(array('post', 'put'))) {
            if (empty($this->request->data['Admin'])) {
                $this->Session->setFlash(__('Invalid data. Please try again'));
            } else {
                $this->request->data['Admin']['plain_password'] = $this->request->data['Admin']['password'];
                $this->Admin->id = $this->Auth->user('id');
                if (!$this->Admin->save($this->request->data['Admin'])) {
                    $this->Session->setFlash(__('An error has occurred. Please try again'), 'flash-error', array());
                } else {
                    $this->_reload($this->request->data['Admin']);
                    $this->Session->setFlash(__('Change password successfully'), 'flash-success', array());
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
    }

    private function _reload($data)
    {
        foreach ($data as $key => $val) {
            $this->Session->write('Auth.User.' . $key, $val);
        }
    }

}
