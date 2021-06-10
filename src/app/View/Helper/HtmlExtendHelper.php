<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP HtmlExtend
 * @author anhhh11
 */
class HtmlExtendHelper extends HtmlHelper
{

    public $helpers = array("Html", "Form");
    const THUMBOR_URL = 'https://thumb.cdn.gamota.net/unsafe/full-fit-in/';

    public function imageUrl($path)
    {
        return $this->assetUrl(IMAGES_URL . $path);
    }

    public function cmsUserFiles($name, $value = null, $nameBtn, $typeMedia = 'images', $options = array(), $formName = 'appForm', $show_as_thumb = false, $save_as_thumb = false)
    {

        $strOptions = '';
        if (count($options) > 0) {
            foreach ($options as $keyOptions => $valueOptions) {
                $strOptions .= $keyOptions . '="' . $valueOptions . '" ';
            }
        }
        $linkMedia = Router::url('/', true) . 'js/media_files/browse.php?type=' . $typeMedia;
        $get_thumb_func = "function get_thumb(url){" .
                "var part = url.split('/');" .
                "part.splice(part.indexOf('post_imgs')+1,0,'.thumbs');" .
                "return part.join('/');" .
                "};";
        $url_show = $show_as_thumb ? 'get_thumb(url)' : 'url';
        $url_save = $save_as_thumb ? 'get_thumb(url)' : 'url';
        $xhtml = '<script type="text/javascript">' .
                $get_thumb_func .
                'function openKCFinder' . $name . '(field) {
				$(\'#formLoading\').removeClass().addClass(\'loading\').fadeIn();
				window.KCFinder = {
					callBack: function(url) {
						field.value = ' . $url_save . ';
						window.KCFinder = null;
						document.getElementById(\'image_' . $name . '\').src = ' . $url_show . ';
						    $(\'#image_' . $name . '\').removeClass("hidden");
					}
				};
				window.open(\'' . $linkMedia . '\', \'kcfinder_textbox\',\'status=0, toolbar=0, location=0, menubar=0, directories=0,resizable=1, scrollbars=0, width=950, height=450\' );
				$(\'#formLoading\').removeClass().fadeOut();
			}</script>';



        $xhtml .= '<input value="' . $value . '" type="text" name="Image[path][' . $name . ']" id="' . $name . '"' . $strOptions . 'class="hidden">';
        $xhtml .= '<a href="javascript:void(0);" onclick="openKCFinder' . $name . '(' . $formName . '.' . $name . ')" class="btn yellow" style="position: relative; z-index: 1;"><i class="fa fa-plus"></i>' . $nameBtn . '</a>';
        return $xhtml;
    }

    private $_includedStyleSheets = array();
    private $_includedJSs = array();

    public function css($path, $options = array())
    {

        if (!is_array($path)) {
            if (isset($this->_includedStyleSheets[$path])) {
                return null;
            }
        }
        $this->_includedStyleSheets[$path] = true;
        return parent::css($path, $options);
    }

    public function script($path, $options = array())
    {
        if (!is_array($path)) {
            if (isset($this->_includedJSs[$path])) {
                return null;
            }
        }
        $this->_includedJSs[$path] = true;
        return parent::script($path, $options);
    }

    public function get_image_from_link($imageUrl, $options = array(), $webp = true, $useThumb = true)
    {
        if($imageUrl == ''){
            $imageUrl = '/img/noimage.png';
        }
        
        $default_options = array('alt' => '', 'class' => 'lazy');
        $using_options = array_merge($default_options, $options);
        $using_options['alt'] = urlencode($using_options['alt']);
        $using_path = $imageUrl;
        //unset($using_options['height']);
      
        $using_options['data-src'] = $using_path;
        return $this->image($using_path, $using_options);
    }

    public function get_image($path, $options = array())
    {
        if(filter_var($path, FILTER_VALIDATE_URL)){
            return $this->get_image_from_link($path, $options);
        }
        $default_options = array('alt' => '');
        $using_options = array_merge($default_options, $options);
        $file = WWW_ROOT . $path;
        if (is_file($file) && file_exists($file)) {
            $using_path = $path;
        } else {
            $using_options['alt'] = urlencode($using_options['alt']);
            $using_path = "/img/noimage.png";
        }
        $using_options['data-src'] = $using_path;
        return $this->image($using_path, $using_options);
    }

    public function get_image_url($path, $options = array())
    {
        $file = WWW_ROOT . DS . strstr($path, '/img/post_imgs');
        $img_url = $this->url($path);
        $default_options = array(
            'width' => '50', 'height' => '50', 'alt' => 'alt', 'path' => ''
        );
        $using_options = array_merge($default_options, $options);
        if (empty($path) || !(is_file($file) && file_exists($file))) {
            return "//placeholdit.imgix.net/~text?"
                    . "txtsize=32&"
                    . "txtcolor=red&"
                    . "txt={$using_options['alt']}&"
                    . "w={$using_options['width']}&"
                    . "h={$using_options['height']}";
        } else {
            return $img_url;
        }
    }

    public function get_main_image($images)
    {
        if (empty($images['main_images']) && empty($images['normal_images'])) {
            $main_images = array_values(array_filter($images, function($image) {
                        return $image['is_main'];
                    }));
            $normal_images = array_values(array_filter($images, function($image) {
                        return !$image['is_main'];
                    }));
            $images = compact('normal_images', 'main_images');
        }
        if (!empty($images['main_images'])) {
            $main_image = $images['main_images'][0];
        } else if (!empty($images['normal_images'])) {
            $main_image = $images['normal_images'][0];
        } else {
            $main_image = array(
                'path' => '',
                'thumb_path' => ''
            );
        }
        $main_image['url'] = $this->get_image_url($main_image['path'], array('width' => 360, 'height' => 360));
        $main_image['thumb_url'] = $this->get_image_url($main_image['thumb_path'], array('width' => 100, 'height' => 100));
        return $main_image;
    }

    public function get_normal_images($images)
    {
        if (empty($images['main_images']) && empty($images['normal_images'])) {
            $main_images = array_filter($images, function($image) {
                return $image['is_main'];
            });
            $normal_images = array_filter($images, function($image) {
                return !$image['is_main'];
            });
            $images = compact('normal_images', 'main_images');
        }
        return array_map(function($normal_image) {
            $normal_image['url'] = $this->get_image_url($normal_image['path'], array('width' => 360, 'height' => 360));
            $normal_image['thumb_url'] = $this->get_image_url($normal_image['thumb_path'], array('width' => 100, 'height' => 100));
            return $normal_image;
        }, $images['normal_images']);
    }
    public function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }
    
    public function slug($str)
    {
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
        $str = preg_replace('/"/', ' ', $str);
        $str = preg_replace('!\s+!', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = strtolower($str);
        return $str;
    }
    public function get_thumb($imageUrl, $options = array(), $webp = true) {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            // is mobile
            $webp = false;
        }
        $default_options = array('alt' => '', 'class' => '', 'width' => 200, 'height' => 100);
        $using_options = array_merge($default_options, $options);
        $using_options['alt'] = urlencode($using_options['alt']);
        if ($webp) {
            $using_path = self::THUMBOR_URL . (isset($using_options['width']) ? $using_options['width'] : 0) . 'x' . (isset($using_options['height']) ? $using_options['height'] : 0) . '/smart/filters:format(webp)/' . $imageUrl;
        } else {
            $using_path = self::THUMBOR_URL . (isset($using_options['width']) ? $using_options['width'] : 0) . 'x' . (isset($using_options['height']) ? $using_options['height'] : 0) . '/' . $imageUrl;
        }
        return $using_path;
    }
}
