<?php

App::uses('AppModel', 'Model');

class Admin extends AppModel
{

    public $useTable = 'admins';
    public $actsAs = array('Containable');
    public $validate = array(
        'email' => array(
            'Valid username' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter a valid Email address!'
            ),
            'Already exists' => array(
                'rule' => 'isUnique',
                'message' => 'This email is already registered in our database!'
            )
        ),
    );

}

?>