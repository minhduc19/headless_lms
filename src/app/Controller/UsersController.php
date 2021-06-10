<?php

App::uses('AppController', 'Controller');

class UsersController extends AppController
{

    public $uses = array('User');
    public $components = array('CRUD', 'ExtendControl');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function isAuthorized($user)
    {
        if ($user['type'] == 'admin') {
            return true;
        }
        return false;
    }

    public function admin_index()
    {
        $this->set('title_for_layout', __('List users'));
        $this->set('title', __('Users management'));
        $this->set('small_title', __('Users'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List users'), 'slug' => '#'));
        $this->set('items', $this->CRUD->basic_paginated_model('User', 'email', array(
                    'conditions' => array_filter($this->request->query)
        )));
    }

}