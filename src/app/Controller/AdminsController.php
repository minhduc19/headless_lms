<?php

App::uses('AppController', 'Controller');

class AdminsController extends AppController
{

    public $uses = array('Admin');
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
        $this->set('title_for_layout', __('List admins'));
        $this->set('title', __('Admins management'));
        $this->set('small_title', __('Admins'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List admins'), 'slug' => '#'));
        $this->set('items', $this->CRUD->basic_paginated_model('Admin', 'name', array(
                    'conditions' => array_filter($this->request->query)
        )));
    }

    public function admin_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Admin\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Admins'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Admin, $id, function($item) {
            $this->set('item', $item);
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function admin_delete($id = null)
    {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Admin, true);
    }

    public function admin_delete_checked()
    {
        $this->CRUD->delete_checked($this->Admin, true, 'index');
    }


    public function admin_add($id = null)
    {
        $this->set('title', __('Admin'));
        $this->set('small_title', __('Add Admin'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Admin'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Admin'), 'slug' => '#'));
        if ($this->request->is('post')) {
            $this->ExtendControl->if_crud_complete_then(function() use($id) {
                $this->redirect(array('action' => 'admin_edit', $this->Admin->getLastInsertID()));
            }, $this->Admin->saveAll($this->request->data, array('deep' => true)));
        }
    }
    
    public function admin_edit($id = null)
    {
        $this->set('title', __('Admins'));
        $this->set('small_title', __('Edit Admin'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Admins'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Admin'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Admin, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                $this->ExtendControl->if_crud_complete_then(function() use($id) {
                    $this->redirect(array('action' => 'admin_index'));
                }, $this->Admin->saveAll($this->request->data, array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }

}