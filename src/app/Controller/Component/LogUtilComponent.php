<?php

App::uses('Component', 'Controller');

class LogUtilComponent extends Component
{

    public function insert_admin($user_id, $ip, $note, $options = array())
    {

        $dialog = ClassRegistry::init('ChatAdminLog');
        $dialog->create();
        return $dialog->save(array(
                    'ChatAdminLog' => array(
                        'user_id' => $user_id,
                        'type' => isset($options['type']) ? $options['type'] : '',
                        'controller' => isset($options['controller']) ? $options['controller'] : '',
                        'action' => isset($options['action']) ? $options['action'] : '',
                        'priority' => isset($options['priority']) ? $options['priority'] : 3,
                        'ip' => $ip,
                        'time' => time(),
                        'description' => $note,
                        'old_data' => isset($options['old_data']) ? $options['old_data'] : '',
                        'new_data' => isset($options['new_data']) ? $options['new_data'] : ''
                    ),
        ));
    }

}
