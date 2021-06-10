<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel
{
    
    public $actsAs = array('Containable');
    public $validate = array();
//    function check_password()
//    {
//        if (strcmp($this->data['User']['password'], $this->data['User']['confirm_password']) == 0) {
//            return true;
//        }
//        return false;
//    }
    
//    public function check_current_password()
//    {
//        $passwordHasher = new SimplePasswordHasher();
//        $this->id = AuthComponent::user('id');
//        $password = $this->field('password');
//        return($passwordHasher->hash($this->data['User']['current_password']) == $password);
//    }
    
    public function beforeSave($options = array())
    {
        if (!$this->id) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                    $this->data[$this->alias]['password']
            );
        } else {
            if (!empty($this->data[$this->alias]['password'])) {
                $passwordHasher = new SimplePasswordHasher();
                $this->data[$this->alias]['password'] = $passwordHasher->hash(
                        $this->data[$this->alias]['password']
                );
            }
        }
        return true;
    }

}
