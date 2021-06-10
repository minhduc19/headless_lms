<?php

App::uses('Component', 'Controller');

class SignComponent extends Component
{

    public function verify($params, $sign, $secret = null)
    {
        if($secret == null){
            $secret = Configure::read('API_SECRET_KEY');
        }
        ksort($params);
        $str = implode("|", $params);
        $hash = sha1($str . "|" . $secret);
//        var_dump($str . "|" . $secret);
//        echo $hash;die();
        if ($hash == $sign) {
            return true;
        }
        return false;
    }
    
}
