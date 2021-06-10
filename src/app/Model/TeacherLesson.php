<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class TeacherLesson extends AppModel
{
    
    public $actsAs = array('Containable');
    public $hasMany = [
        'TeacherLessonSkill',
        'TeacherScene' => [
            'order' => ['TeacherScene.sort_order' => 'DESC'],
            'dependent' => true
        ]];
    public $validate = array(
        'title' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
            ),
        )
    );

}
