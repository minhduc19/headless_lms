<?php

App::uses('AppController', 'Controller');

class TagsController extends AppController
{

    public $uses = array('Tag');
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
        $this->set('title_for_layout', __('List tags'));
        $this->set('title', __('Tags management'));
        $this->set('small_title', __('Tags'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List tags'), 'slug' => '#'));
        $this->set('items', $this->CRUD->basic_paginated_model('Tag', 'name', array(
                    'conditions' => array_filter($this->request->query),
                    'order' => ['Tag.popular' => 'DESC']
        )));
    }

    public function admin_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Tag\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Tags'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Tag, $id, function($item) {
            $this->set('item', $item);
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function admin_delete($id = null)
    {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Tag, true);
    }

    public function admin_delete_checked()
    {
        $this->CRUD->delete_checked($this->Tag, true, 'index');
    }


    public function admin_add($id = null)
    {
        $this->set('title', __('Tag'));
        $this->set('small_title', __('Add Tag'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Tags'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Tag'), 'slug' => '#'));
        if ($this->request->is('post')) {
            $this->ExtendControl->if_crud_complete_then(function() use($id) {
                $this->redirect(array('action' => 'admin_edit', $this->Tag->getLastInsertID()));
            }, $this->Tag->saveAll($this->request->data, array('deep' => true)));
        }
    }
    
    public function admin_edit($id = null)
    {
        $this->set('title', __('Tags'));
        $this->set('small_title', __('Edit Tag'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Tags'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Tag'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Tag, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                $this->ExtendControl->if_crud_complete_then(function() use($id) {
                    $this->redirect(array('action' => 'admin_index'));
                }, $this->Tag->saveAll($this->request->data, array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }

}