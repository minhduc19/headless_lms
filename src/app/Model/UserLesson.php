<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class UserLesson extends AppModel
{
    
    public $actsAs = array('Containable');
    public $belongsTo = ['Lesson'];
    public $validate = array();

}
