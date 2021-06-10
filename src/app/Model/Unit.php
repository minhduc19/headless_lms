<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class Unit extends AppModel
{
    
    public $actsAs = array('Containable');
    public $belongsTo = ['Lesson'];
    public $hasMany = ['Scene' => ['dependent' => true]];
    public $validate = array(
        'title' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
            ),
        )
    );

//    public function beforeDelete($cascade = true) {
//        $this->Scene->deleteAll(['Scene.unit_id' => $this->id], $cascade);
//        return true;
//    }
}
