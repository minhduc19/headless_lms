<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP OptionHelper
 * @author anhhh11
 */
class OptionHelper extends AppHelper {

	const RECTANGLE = 0;
	const RADIOBOX = 1;
	const SELECTBOX = 2;

	public $helpers = array();

	public function options_of_group_to_id_val_pairs(&$group_option) {
		$option = &$group_option["Option"];
		$option_text = &$option["OptionText"];
		$keyValPairs = array_combine(Hash::extract($option_text, '{n}.id'), Hash::extract($option_text, '{n}.name'));
		return $keyValPairs;
	}

}
