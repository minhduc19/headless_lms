<?php

echo __('Hi %s,', $user['Admin']['last_name']);
echo '<br />';
echo __('We has received a request to reset the password for your account');
echo '<br />';
echo __('If you did not request to reset your password, please ignore this email.');
echo '<br />';
echo __('Please click the following link to reset your password. This link will be expired after 30 minutes.');
echo '<br />';
echo $this->Html->url(array('controller' => 'public', 'action' => 'reset_password', 'code' => $code, 'full_base' => true));
