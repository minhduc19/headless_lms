<?php

App::uses('AppController', 'Controller');

class TeachersController extends AppController
{

    public $uses = array('Teacher', 'TeacherSkill');
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
        $this->set('title_for_layout', __('List teachers'));
        $this->set('title', __('Teachers management'));
        $this->set('small_title', __('Teachers'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('List Teachers'), 'slug' => '#'));
        $this->set('items', $this->CRUD->basic_paginated_model('Teacher', 'name', array(
                    'conditions' => array_filter($this->request->query)
        )));
    }

    public function admin_detail($id)
    {
        $this->set('title', __('Function \'s detail'));
        $this->set('small_title', __('Teacher\'s detail'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Teachers'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Detail'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Teacher, $id, function($item) {
            $this->set('item', $item);
            $this->request->data = $item;
            $this->request->data['_method'] = 'GET';
        });
    }

    public function admin_delete($id = null)
    {
        $this->CRUD->ajax_delete_individual_by_id($id, $this->Teacher, true);
    }

    public function admin_delete_checked()
    {
        $this->CRUD->delete_checked($this->Teacher, true, 'index');
    }


    public function admin_add($id = null)
    {
        $this->set('title', __('Teacher'));
        $this->set('small_title', __('Add Teacher'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Teacher'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Add Teacher'), 'slug' => '#'));
        if ($this->request->is('post')) {
            $this->Teacher->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->ExtendControl->if_crud_complete_then(function() use($id) {
                $id = $this->Teacher->getLastInsertID();
                $this->updateSkills($id, [], $this->request->data['TeacherSkill']);
                $this->redirect(array('action' => 'admin_edit', $id));
            }, $this->Teacher->saveAll($this->request->data['Teacher'], array('deep' => true)));
        }
    }
    
    public function admin_edit($id = null)
    {
        $this->set('title', __('Teachers'));
        $this->set('small_title', __('Edit Teacher'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Home'), 'slug' => '/'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Teachers'), 'slug' => '.'));
        $this->Breadcrumb->addBreadcrumb(array('title' => __('Edit Teacher'), 'slug' => '#'));
        $this->CRUD->detail_of($this->Teacher, $id, function($item) use ($id) {
            if ($this->request->is('put')) {
                $this->Teacher->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->ExtendControl->if_crud_complete_then(function() use($id, $item) {
                    $oldSkills = [];
                    if(!empty($item['TeacherSkill'])){
                        foreach($item['TeacherSkill'] as $skill){
                            $oldSkills[$skill['id']] = $skill;
                        }
                    }

                    $this->updateSkills($id, $oldSkills, $this->request->data['TeacherSkill']);
                    $this->redirect(array('action' => 'admin_index'));
                }, $this->Teacher->saveAll($this->request->data['Teacher'], array('deep' => true)));
            } else {
                $this->request->data = $item;
            }
            $this->set('item', $item);
        });
    }
    
    private function updateSkills($teacher_id, $old_skills, $new_skills){
        if(!empty($new_skills['name'])){
            $arrSkills = [];
            foreach($new_skills['name'] as $key => $skill_name){
                if(isset($old_skills[$key])){ // Update Unit
                    $this->TeacherSkill->id = $key;
                    $this->TeacherSkill->saveField('name', $skill_name);
                    $this->TeacherSkill->saveField('sort_order', $new_skills['sort_order'][$key]);
                } else { // Insert unit
                    $arrSkills[] = ['TeacherSkill' => [
                            'name' => $skill_name,
                            'teacher_id' => $teacher_id,
                            'sort_order' => $new_skills['sort_order'][$key],
                        ]
                    ];
                }
            }
            if(!empty($arrSkills)){
                $this->TeacherSkill->saveAll($arrSkills);
            }
        }
        
        // remove old skills
        if(!empty($old_skills)){
            foreach ($old_skills as $skill_id => $skill) {
                if(!isset($new_skills['name'][$skill_id])){
                    $this->TeacherSkill->delete($skill_id);
                }
            }
        }
        
        
    }

    public function admin_ajax_add_skill() {
        $this->layout = 'ajax';
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
    }
}