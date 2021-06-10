<?php

App::uses('Helper', 'View');

class BreadcrumbHelper extends Helper {

    var $helpers = array('Html');
    var $sHome = 'Home';
    var $sAdmin = 'Admin';

    public function display($aBreadcrumbs) {

	if (is_array($aBreadcrumbs)) {
	    $returnHTML = '<ol class="page-breadcrumb">';
	    foreach ($aBreadcrumbs as $key => $value) {
		if ($key == 0) {
		    $returnHTML .= '<li><a href="' . $this->Html->url($value['slug']) . '"><i class="fa fa-dashboard"></i>' . $value['title'] . '<i class="fa fa-angle-left"></i>' . '</a></li>';
		} elseif ($key != count($aBreadcrumbs) - 1) {
		    $returnHTML .= '<li>' . $this->Html->link($value['title'], $value['slug']) . '<i class="fa fa-angle-left"></i>' . '</li>';
		} else {
		    $returnHTML .= '<li class="active">' . $value['title'] . '</li>';
		}
	    }
	}

	$returnHTML .= '</ol>';
	return $returnHTML;
    }

}