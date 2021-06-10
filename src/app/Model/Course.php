<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class Course extends AppModel
{
    
    public $actsAs = array('Containable');
    public $hasMany = [
        'Chapter' => [
            'order' => ['Chapter.sort_order' => 'DESC'],
            'dependent' => true
            ],
        'CourseTag' => ['dependent' => true], 
        'Lesson' => [
            'order' => ['Lesson.sort_order' => 'DESC'],
            'dependent' => true
            ]
        ];
    public $validate = array(
        'title' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
            ),
        )
    );
    

//    public function beforeDelete($cascade = true) {
//        $this->CourseTag->deleteAll(['CourseTag.course_id' => $this->id], $cascade);
//        $this->Chapter->deleteAll(['Chapter.course_id' => $this->id], $cascade);
//        $this->Lesson->deleteAll(['Lesson.course_id' => $this->id], $cascade);
//        return true;
//    }

}
