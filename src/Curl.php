<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

final class Curl
{
    /**
     * request
     *
     * @param string $url 请求URL
     * @param string $method 请求方式
     * @param null $data 请求数据
     * @param int $timeout 超时时间
     * @param array $header Header头
     *
     * @return mixed
     * 
     *
     */
    public static function request($url, $method, $data = null, $timeout = 5, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, true); // 显示返回的Header区域内容
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在

        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $result = curl_exec($ch);

        if (false === $result) {
            curl_getinfo($ch);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * get
     *
     * @param string $url 请求URL
     * @param int $timeout 超时时间 3s（单位:s）
     * @param array $header Header头
     * @param array $timeout Header头
     * @return mixed
     * 
     *
     */
    public static function get($url, $timeout = 3, $header = [])
    {
        return self::request($url, 'GET', [], $timeout, $header);
    }

    /**
     * post
     *
     * @param string $url 请求URL
     * @param array $data 请求数据
     * @param int $timeout 超时时间
     * @param array $header Header头
     *
     * @return mixed
     * 
     *
     */
    public static function post($url, $data, $timeout = 5, $header = [])
    {
         if (is_array($data)){
            $postdata = http_build_query($data);
        }else{
            $postdata = '';
        }
        return self::request($url, 'POST', $postdata, $timeout, $header);
    }

    /**
    *以文件形式获取参数
    * @param $url
    * @param $getData
    * @return false|string
    */
    public static function httpGet($url,$getData) {
       if (is_array($getData))
           $url = $url.'?'.http_build_query($getData);
       $options = array(
        'http' => array(
            'method'  => 'GET',
            'header'  => 'Content-type:application/x-www-form-urlencoded',
            'timeout' => 3 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        return $result;
   }

   /**
     * 发起https get请求
     */
    public static function _httpsGet($url,$getData){
        if (is_array($getData))
            $url = $url.'?'.http_build_query($getData);
        return self::request($url, 'GET', [], '', []);
    }

    /**
     * 设置好请求头的post x-www-form-urlencoded 以文件形式获取参数
     * @param $url
     * @param $post_data
     * @return false|string
     */
    public static function httpPost($url,$post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 3 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
}