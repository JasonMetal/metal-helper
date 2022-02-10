<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

/**
 * Image,base64 工具类
 *
 * @package  metal\helper
 */
final class Image {


    /**
     * base64格式编码转换为图片并保存对应文件夹
     * @param $base64_image_content $path [路径以"/"结尾]
     * @param $path
     * @return array|bool
     */
    public static function base64_to_image($base64_image_content, $path, $fileName = null) {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            if (!file_exists($path)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($path, 0700, true);
            }
            $new_name = $fileName ? $fileName . ".{$type}" : time() . substr(md5(rand()), 0, 6) . ".{$type}";
            $new_file = $path . $new_name;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return [
                    'file_path' => $new_file,
                    'file_name' => $new_name,
                    'file_type' => $type,
                    "file_size" => filesize($new_file),
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 图片转成base64位编码格式
     * @param $ImageFile [图片路径]
     * @return bool|string
     */
    public static function image_to_base64Encode($ImageFile) {
        @$imageInfo = getimagesize($ImageFile);
        if (preg_match("#(http|https)://(.*\.)?.*\..*#i", $ImageFile)) {
            $result = @file_get_contents($ImageFile);
            if ($result) {
                $base64Image = chunk_split(base64_encode($result));
            } else {
                return false;
            }
        } else if (file_exists($ImageFile) || is_file($ImageFile)) {
            $imageData   = fread(fopen($ImageFile, 'r'), filesize($ImageFile));
            $base64Image = chunk_split(base64_encode($imageData));
        } else {
            return false;
        }
        return 'data:' . $imageInfo['mime'] . ';base64,' . $base64Image;
    }

    /**
     * 获取所有图片链接
     * @return array
     */
    public static function analyze_remote_img($str) {
        preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $str, $match);
        $imglist = [];
        foreach ($match[1] as $imgpath) {
            if (preg_match("/\.(gif|jpg|jpeg|png|bmp)$/i", strtolower($imgpath))) {
                $imglist[md5($imgpath)] = $imgpath;
            }
        }
        return $imglist;
    }


    /**
     * 获取图片的文件路径（去除图片链接的域名）
     * http://image.***.com/201603/29/1231232131.png
     * return 201603/29/1231232131.png
     */
    public static function getImageRelUrl($http_url) {
        if (strpos($http_url, 'http://') === 0) {
            $array = explode('/', $http_url);
            unset($array[0]);
            unset($array[1]);
            unset($array[2]);
            $url = implode('/', $array);
            return $url;
        }
        return $http_url;
    }

    /**
     * 图片路径转换（数据库路径转http连接路径）
     * @param string $domain 域名
     * @param string $url 数据库路径
     * @param int $type 返回类型 1返回cdn加速地址 2返回源图片地址
     * @return string
     */
    public static function constructImageUrl($domain = '', $url = '', $type = 1) {
        if (empty($domain) || empty($url)) {
            return $url;
        }
        if (strpos($url, 'http://') === 0) {
            return $url;
        }
        $domain_array = base::load_config("common", "ucloud_ftp");
        if ($type == 1) {
            //图片cdn加速地址
            $rtnstring = 'http://' . $domain_array[$domain]['cdn_domain'] . '/' . $url;
        } else {
            //图片源地址
            $rtnstring = 'http://' . $domain_array[$domain]['domain'] . '/' . $url;
        }
        return $rtnstring;
    }

    /**
     * 图片规格转换
     *
     * @param string $thumbpath 480408图片路径
     * @param 需要生成的图片规格 $norm [值为 '120120','240240','360360','480480']
     * @return string
     */
    public static function construct_shoes_img($thumbpath, $norm = '480480') {
        $img_norm = base::load_config("common", "image_norms");
        if (!$img_norm[$norm]) {
            $rtn_path = $thumbpath;
        } else {
            $ucloud_norm = explode('x', $img_norm[$norm]);
            $rtn_path    = $thumbpath . '?iopcmd=thumbnail&type=6&width=' . $ucloud_norm[0] . '&height=' . $ucloud_norm[1];
        }
        return $rtn_path;
    }

    /**
     * 图片裁剪
     * ucloud 图片裁剪
     * @param string $url 480408图片路径
     * @param int $width 裁剪后的图片宽度
     * @param int $height 裁剪后的图片高度
     * @param int $ax 锚点 x 坐标
     * @param int $ay 锚点 y 坐标
     */
    public static function construct_imageCrop($url, $width, $height, $ax, $ay) {
        $crop_img = $url . '?iopcmd=crop&width=' . $width . '&height=' . $height . '&ax=' . $ax . '&ay=' . $ay;
        return $crop_img;
    }


}