<?php

App::uses('AppController', 'Controller');

class SkillsController extends AppController
{

    public $uses = array('TeacherSkill');
    public $components = array('CRUD', 'ExtendControl');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function isAuthorized($user)
    {
        if ($user['type'] == 'teacher') {
            return true;
        }
        return false;
    }

    public function teacher_index()
    {
        $this->set('title_for_layout', __('List skills'));
        $this->set('title', __('Skills management'));
        $this->set('small_title', __('Skills'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List skills'), 'slug' => '#'));
        $this->request->query['teacher_id'] = $this->auth_user['id'];
        $this->set('items', $this->CRUD->basic_paginated_model('TeacherSkill', 'name', array(
                    'conditions' => array_filter($this->request->query),
                    'order' => ['TeacherSkill.sort_order' => 'DESC']
        )));
    }

    public function teacher_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Skill\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Skills'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->TeacherSkill, $id, function($item) {
            $this->set('item', $item);
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function teacher_delete($id = null)
    {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->TeacherSkill, true);
    }

    public function teacher_delete_checked()
    {
        $this->CRUD->delete_checked($this->TeacherSkill, true, 'index');
    }


    public function teacher_add($id = null)
    {
        $this->set('title', __('Skills'));
        $this->set('small_title', __('Add Skill'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Skills'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Skill'), 'slug' => '#'));
        if ($this->request->is('post')) {
            $this->request->data['TeacherSkill']['teacher_id'] = $this->auth_user['id'];
            $this->ExtendControl->if_crud_complete_then(function() use($id) {
                $this->redirect(array('action' => 'teacher_edit', $this->TeacherSkill->getLastInsertID()));
            }, $this->TeacherSkill->saveAll($this->request->data, array('deep' => true)));
        }
    }
    
    public function teacher_edit($id = null)
    {
        $this->set('title', __('Skills'));
        $this->set('small_title', __('Edit Skill'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Skills'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Skill'), 'slug' => '#'));
        $this->CRUD->detail_of($this->TeacherSkill, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                $this->ExtendControl->if_crud_complete_then(function() use($id) {
                    $this->redirect(array('action' => 'teacher_index'));
                }, $this->TeacherSkill->saveAll($this->request->data, array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }

}