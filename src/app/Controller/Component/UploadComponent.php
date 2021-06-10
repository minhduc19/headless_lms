<?php

App::uses('Component', 'Controller');

class UploadComponent extends Component {

    public function upload($file = array(), $options = array()) {
        if ($file['error'] != 0) {
            return FALSE;
        }
        $folder = $options['type'] . DS . date('Ymd');
        $file_name = PseudoCrypt::udihash(microtime(TRUE)) . $file['name'];
        $full_path = $folder . DS . $file_name;
        $real_path = WWW_ROOT . 'files' . DS . $full_path;
        $real_folder = WWW_ROOT . 'files' . DS . $folder;
        if (!is_dir($real_folder)) {
            if (!mkdir($real_folder, 0777, true)) {
                return FALSE;
            }
        }
        if (move_uploaded_file($file['tmp_name'], $real_path)) {
            return Router::fullbaseUrl() . DS . 'files' . DS . $full_path;
        } else {
            return FALSE;
        }
    }

    public function upload_unit($lesson_id,$file) {
        $index_file = 'index.html';
        if ($file['error'] != 0) {
            return FALSE;
        }
        $source = $file["tmp_name"];
        $type = $file["type"];

        $name = explode(".", $file['name']);
        $base_name = $name[0];
        $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
        if (!in_array($type, $accepted_types)) {
            return false;
        }

        $continue = strtolower($name[1]) == 'zip' ? true : false;
        if (!$continue) {
            return false;
        }

        $filename = PseudoCrypt::udihash(microtime(TRUE)) . $this->slugify($file['name']);
        $real_path = WWW_ROOT . 'units' . DS . $lesson_id . DS . $filename;
        $real_folder = WWW_ROOT . 'units' . DS . $lesson_id;
        if (!is_dir($real_folder)) {
            if (!mkdir($real_folder, 0777, true)) {
                return FALSE;
            }
        }
        if (move_uploaded_file($file['tmp_name'], $real_path)) {
            $zip = new ZipArchive();
            $x = $zip->open($real_path);
            if ($x === true) {
                $name = explode(".", $filename);
                $unit_folder = $this->slugify($name[0]);
                $unit_full_folder = $real_folder . DS . $unit_folder;
                system("rm -rf $unit_full_folder");
                $zip->extractTo($unit_full_folder);
                $zip->close();
                //unlink($real_path);
                if(!file_exists($unit_full_folder . DS . $index_file) && file_exists($unit_full_folder . DS . $base_name . DS . $index_file)){
                    //TODO: Move files from sub folder
                    $this->rcopy($unit_full_folder . DS . $base_name, $unit_full_folder);
                    system("rm -rf " . $unit_full_folder . DS . $base_name);
                }
                if(!file_exists($unit_full_folder . DS . $index_file)){
                    return [false, false];
                }
                $url = Router::url('/', true) . "units/$lesson_id/" . $unit_folder . "/$index_file";
                $zip_url = Router::url('/', true) . "units/$lesson_id/$filename";
                return [$url, $zip_url];
            }
        }
        return [false, false];
    }
    
    private function rcopy($src, $dst) {
        if (is_dir ( $src )) {
            if(!is_dir($dst)){
                mkdir ( $dst );
            }
            $files = scandir ( $src );
            foreach ( $files as $file ){
                if ($file != "." && $file != ".."){
                    $this->rcopy ( "$src/$file", "$dst/$file" );
                }
            }
        } else if (file_exists ( $src )){
            copy ( $src, $dst );
        }
    }

    private function recurse_copy($src, $dst) {
        $dir = opendir($src);
        if(!is_dir($dst)){
            mkdir($dst);
        }
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function upload_img($file = array(), $options = array()) {
        if ($file['error'] != 0) {
            return FALSE;
        }
        $info = getimagesize($file['tmp_name']);
        if ($info === FALSE) {
            return false;
        }

        if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
            return false;
        }
        $folder = $options['type'] . DS . date('Ymd');
        $file_name = PseudoCrypt::udihash(microtime(TRUE)) . $this->slugify($file['name']);
        $full_path = $folder . DS . $file_name;
        $real_path = WWW_ROOT . 'img' . DS . 'post_imgs' . DS . $full_path;
        $real_folder = WWW_ROOT . 'img' . DS . 'post_imgs' . DS . $folder;
        if (!is_dir($real_folder)) {
            if (!mkdir($real_folder, 0777, true)) {
                return FALSE;
            }
        }
        if (move_uploaded_file($file['tmp_name'], $real_path)) {
            return Router::fullbaseUrl() . DS . 'img' . DS . 'post_imgs' . DS . $full_path;
            //return $this->uploadStatic($real_path);
        } else {
            return FALSE;
        }
    }

    private function slugify($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        $str = preg_replace("/([%])|(\?)|(:)/", "", $str);
        $str = strtolower($str);
        $str = str_replace(" ", "-", $str);

        return $str;
    }

}

class PseudoCrypt {
    /* Next prime greater than 62 ^ n / 1.618033988749894848 */

    private static $golden_primes = array(
        //2, 3, 5, 11, 19, 41, 79, 139, 257, 401, 827, 1471, 2591, 4493, 7793
        1, 41, 2377, 147299, 9132313, 566201239, 35104476161, 2176477521929
    );

    /* Ascii :                    0  9,         A  Z,         a  z     */
    /* $chars = array_merge(range(48,57), range(65,90), range(97,122)) */
    private static $chars = array(
        0 => 48, 1 => 49, 2 => 50, 3 => 51, 4 => 52, 5 => 53, 6 => 54, 7 => 55, 8 => 56, 9 => 57, 10 => 65,
        11 => 66, 12 => 67, 13 => 68, 14 => 69, 15 => 70, 16 => 71, 17 => 72, 18 => 73, 19 => 74, 20 => 75,
        21 => 76, 22 => 77, 23 => 78, 24 => 79, 25 => 80, 26 => 81, 27 => 82, 28 => 83, 29 => 84, 30 => 85,
        31 => 86, 32 => 87, 33 => 88, 34 => 89, 35 => 90, 36 => 97, 37 => 98, 38 => 99, 39 => 100, 40 => 101,
        41 => 102, 42 => 103, 43 => 104, 44 => 105, 45 => 106, 46 => 107, 47 => 108, 48 => 109, 49 => 110,
        50 => 111, 51 => 112, 52 => 113, 53 => 114, 54 => 115, 55 => 116, 56 => 117, 57 => 118, 58 => 119,
        59 => 120, 60 => 121, 61 => 122
    );

    public static function base62($int) {
        $key = "";
        while ($int > 0) {
            $mod = $int - (floor($int / 62) * 62);
            $key .= chr(self::$chars[$mod]);
            $int = floor($int / 62);
        }
        return strrev($key);
    }

    public static function udihash($num, $len = 5) {
        $ceil = pow(62, $len);
        $prime = self::$golden_primes[$len];
        $dec = ($num * $prime) - floor($num * $prime / $ceil) * $ceil;
        $hash = self::base62($dec);
        return str_pad($hash, $len, "0", STR_PAD_LEFT);
    }

}
