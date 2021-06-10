<?php

App::uses('AppModel', 'Model');

class Config extends AppModel
{

    public $useTable = 'configs';
    public $actsAs = array('Containable');
    public $validate = array();

}

?>