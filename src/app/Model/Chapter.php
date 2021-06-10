<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class Chapter extends AppModel
{
    
    public $actsAs = array('Containable');
    public $hasMany = ['Lesson' => [
        'order' => ['Lesson.sort_order' => 'DESC']
    ]];
    public $validate = array(
        'title' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
            ),
        )
    );

}
