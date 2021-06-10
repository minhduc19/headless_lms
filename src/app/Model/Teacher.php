<?php

App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class Teacher extends AppModel
{

    public $useTable = 'teachers';
    public $actsAs = array('Containable');
    public $hasMany = ['TeacherSkill'];
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
    
    function check_password()
    {
        if (strcmp($this->data['Teacher']['password'], $this->data['Teacher']['confirm_password']) == 0) {
            return true;
        }
        return false;
    }

    public function check_current_password()
    {
        $this->id = AuthComponent::user('id');
        $password = $this->field('password');
        return(AuthComponent::password($this->data['Teacher']['current_password']) == $password);
    }

    public function beforeSave($options = array())
    {
        if (!$this->id) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                    $this->data[$this->alias]['password']
            );
        } else {
            if (!empty($this->data[$this->alias]['password'])) {
                $passwordHasher = new SimplePasswordHasher();
                $this->data[$this->alias]['password'] = $passwordHasher->hash(
                        $this->data[$this->alias]['password']
                );
            }
        }
        return true;
    }

}

?>