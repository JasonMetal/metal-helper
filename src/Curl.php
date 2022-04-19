<?php

/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

final class Curl {
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
    public static function request($url, $method, $data = null, $timeout = 5, $header = []) {
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

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
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
     * @Notes  : 支持http https 发送json
     * @param $url
     * @param string $method
     * @param null $data
     * @param bool $https
     * @return :array|bool|string
     * @time   : 2021/5/23/023_23:53
     */
    static function http($url, $method = "GET", $data = NULL, $https = false) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        if ($https) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }
        if (!empty($data)) {
            if ($method != "GET") {
                if ($method == 'POST') {
                    curl_setopt($curl, CURLOPT_POST, true); //请求方式为post请求
                    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
                    curl_setopt(
                        $curl,
                        CURLOPT_HTTPHEADER,
                        ['Content-Type: application/json; charset=utf-8', 'Content-Length:' . strlen($data)]
                    );
                }
                if ($method == 'PUT' || strtoupper($method) == 'DELETE') {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //请求数据
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res     = curl_exec($curl); // 执行操作
        $errorno = curl_errno($curl);
        if ($errorno) {
            return ['errorno' => false, 'errmsg' => $errorno];
        }
        curl_close($curl); // 关闭CURL会话
        //  return json_decode($res, true);
        return $res;
    }


    static function httpCurl($url, $data = '', $method = 'GET', $https = false, $header = [], $timeout = 3) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        if ($https) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method != "GET") {
            if ($method == 'POST') {
                curl_setopt($curl, CURLOPT_POST, true); //请求方式为post请求
            }
            if ($method == 'PUT' || strtoupper($method) == 'DELETE') {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //请求数据
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        //        return json_decode($tmpInfo, true);// 返回数据
        return $tmpInfo; // 返回数据
    }


    /**
     * http 请求的curl
     * @param $url
     * @param string $data
     * @param string $method
     * @param array $header
     * @return bool|string
     */
    static function sendHttpByCurl($url, $data = '', $method = 'GET', $header = []) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        // 解决curl post 请求慢
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // 强制使用ipv4
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); // 强制使用协议1.0

        curl_setopt($curl, CURLOPT_TIMEOUT, 3); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
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
    public static function get($url, $timeout = 3, $header = []) {
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
    public static function post($url, $data, $timeout = 5, $header = []) {
        if (is_array($data)) {
            $postdata = http_build_query($data);
        } else {
            $postdata = '';
        }
        return self::request($url, 'POST', $postdata, $timeout, $header);
    }


    static function httpsCurlJson($url, $data, $timeout, $header = []) {

        $header1  = [
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        ];
        $header   = array_merge($header1, $header);
        $jsonData = json_encode($data);
        return self::httpCurl($url, $jsonData, 'POST', true, $header, $timeout);
    }

    /**
     *以文件形式获取参数
     * @param $url
     * @param $getData
     * @return false|string
     */
    public static function httpGet($url, $getData) {
        if (is_array($getData))
            $url = $url . '?' . http_build_query($getData);
        $options = [
            'http' => [
                'method'  => 'GET',
                'header'  => 'Content-type:application/x-www-form-urlencoded',
                'timeout' => 3 // 超时时间（单位:s）
            ],
        ];
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * 发起https get请求
     */
    public static function _httpsGet($url, $getData) {
        if (is_array($getData))
            $url = $url . '?' . http_build_query($getData);
        return self::request($url, 'GET', [], '', []);
    }

    /**
     * 设置好请求头的post x-www-form-urlencoded 以文件形式获取参数
     * @param $url
     * @param $post_data
     * @return false|string
     */
    public static function httpPost($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options  = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 3 // 超时时间（单位:s）
            ],
        ];
        $context  = stream_context_create($options);
        $result   = file_get_contents($url, false, $context);

        return $result;
    }


}
