<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('AppModel', 'Model');

class Comment extends AppModel
{
    
    public $actsAs = array('Containable');
    public $hasMany = ['Media'];
    public $belongsTo = ['User'];
    public $validate = array();

}
