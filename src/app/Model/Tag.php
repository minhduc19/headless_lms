<?php

App::uses('AppModel', 'Model');

class Tag extends AppModel
{

    public $actsAs = array('Containable');
    public $validate = array();

}

?>