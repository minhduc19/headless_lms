<?php
if (!empty($success)){
//	echo json_encode(array('message' => __('Deleted')));
	echo json_encode(array('redirectUrl' => $this->Html->url(array('action' => 'teacher_index'))));
} else {
	echo json_encode(array('error' => __('This row doesn\'t exists')));
}