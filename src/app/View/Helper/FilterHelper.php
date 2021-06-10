<?php

App::uses("Helper", "View/Helper");

class FilterHelper extends Helper {

    public $helpers = array('Html', 'Form');
    private $options = array();

    public function beforeRender($viewFile) {
	$this->options['url'] = array_merge($this->request->params['pass'], $this->request->params['named']);
	if (!empty($this->request->query)) {
	    $this->options['url']['?'] = $this->request->query;
	} else {
	    $this->options['url']['?'] = array();
	}
	parent::beforeRender($viewFile);
    }

    public function link($text, $filters) {
	$active = FALSE;
	$url = $this->options['url'];
	foreach ($filters as $key => $value) {
	    if (isset($url['?'][$key]) && $url['?'][$key] == $value) {
		$active = TRUE;
	    } else if (empty($url['?'][$key]) && empty($value)) {
		$active = TRUE;
	    }
	    if (empty($value)) {
		unset($url['?'][$key]);
		unset($filters[$key]);
	    }
	}
	$query = array_merge($url['?'], $filters);
	$url['?'] = $query;
	unset($url['page']);
	return $this->Html->link($text, $url, array('class' => $active ? 'active col_sort' : 'col_sort', 'escapeTitle' => FALSE, 'escape' => FALSE));
    }

    public function sort_link($text, $key) {
	$url = $this->options['url'];
	if (isset($url['?']['order']) && $url['?']['order'] == $key) {
	    if (!isset($url['?']['order_by'])) {
		$order_by = 'asc';
	    } else {
		$order_by = $url['?']['order_by'] == 'asc' ? 'desc' : 'asc';
	    }
	} else {
	    $order_by = 'asc';
	}
	if (isset($url['?']['order']) && $url['?']['order'] == $key) {
	    $text .= $order_by == 'desc' ? ' ' . $this->Html->image('sort_asc.png', array('style' => 'float:right')) : ' ' . $this->Html->image('sort_desc.png', array('style' => 'float:right;'));
	} else {
	    $text .= $this->Html->image('sort_both.png', array('style' => 'float:right;'));
	}

	return $this->link($text, array('order' => $key, 'order_by' => $order_by));
    }

    public function begin_form($redirection = 'index', $id = null) {
//	$url = $this->options['url'];
	$url['action'] = 'filter';
	$output = $this->Form->create(FALSE, array('id' => $id, 'url' => $url, 'name' => 'formSearch', 'inputDefaults' => array('label' => false, 'div' => false)));
	$output .= $this->Form->input('redirect', array('type' => 'hidden', 'value' => $redirection));
	return $output;
    }

    public function textbox($filter_name, $options) {
	$options['default'] = empty($this->options['url']['?'][$filter_name]) ? '' : $this->options['url']['?'][$filter_name];
	return $this->Form->input($filter_name, $options);
    }

    public function search_button($text, $options) {
	$options['div'] = FALSE;
	return $this->Form->submit($text, $options);
    }

    public function clear_button($text, $options = array(), $redirect = 'index') {
	$options['div'] = FALSE;
	$url = isset($this->options['url']) ? $this->options['url'] : '';
	$url['action'] = 'clear';
	$url['?']['redirect'] = $redirect;
	$options['onclick'] = 'window.location=\'' . $this->Html->url($url) . '\'; return false';
	return $this->Form->button($text, $options);
    }

    public function end_form() {
	return $this->Form->end();
    }

    public function pagination_select($options) {
	$options['options'] = array(5 => 5, 10 => 10, 20 => 20);
	$options['id'] = 'pagination_select';
	$options['default'] = empty($this->options['url']['?']['limit_page']) ? 5 : $this->options['url']['?']['limit_page'];
	$output = $this->Form->input('limit_page', $options);
	$output .= <<<HERE
<script>
$(function(){
   $('#pagination_select').change(function(){
      $(this).parents('form').submit();
   });
});
</script>
HERE;
	return $output;
    }

}
