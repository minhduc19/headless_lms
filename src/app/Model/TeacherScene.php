<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class TeacherScene extends AppModel
{
    
    public $actsAs = array('Containable');
    public $validate = array();
    public $hasMany = [
        'Media' => ['dependent' => true],
        'Feedback' => ['dependent' => true],
        ];
    public $belongsTo = ['TeacherLesson'];
}
