<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class Lesson extends AppModel
{
    
    public $actsAs = array('Containable');
    public $hasMany = [
        'Unit' => [
            'order' => ['Unit.sort_order' => 'DESC'],
            'dependent' => true
        ],
        'LessonTag' => ['dependent' => true]];
    public $belongsTo = ['Chapter', 'Course'];
    public $validate = array(
        'title' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
            ),
        )
    );

//    public function beforeDelete($cascade = true) {
//        $this->Unit->deleteAll(['Unit.lesson_id' => $this->id], $cascade);
//        $this->LessonTag->deleteAll(['LessonTag.lesson_id' => $this->id], $cascade);
//        return true;
//    }
}
