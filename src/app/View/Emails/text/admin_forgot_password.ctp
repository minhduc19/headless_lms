<?php

echo __('Hi %s,', $user['User']['last_name']);
echo "\n";
echo __('We has received a request to reset the password for your account');
echo "\n";
echo __('If you did not request to reset your password, please ignore this email.');
echo "\n";
echo __('Please click the following link to reset your password. This link will be expired after 30 minutes.');
echo "\n";
echo $this->Html->url(array('controller' => 'public', 'action' => 'reset_password', 'code' => $code, 'full_base' => true));
