<?php

App::uses("Helper", "View/Helper");

class LanguageHelper extends Helper {

    public function translate($data = array()) {
        $arr = array();
        foreach ($data as $key => $val) {
            $arr[$key] = __($val);
        }
        return $arr;
    }
}
