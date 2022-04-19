<?php
/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

final class HttpRequestUtil {
    const TIME_OUT         = 10;
    const CONNECT_TIME_OUT = 1;

    public static function concatParams($params) {
        if (!is_array($params))
            return false;
        $temps = [];
        foreach ($params as $key => $value) {
            $value   = rawurlencode($value);
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        return implode('&', $temps);
    }

    /**
     * http get请求
     * @param string $url 请求url地址
     * @param string|object|array $params
     * @param string $encoding 请求数据的编码
     * @param string $contentType 请求数据的内容格式
     * @param string $returnEncoding
     * @return mixed|string
     */
    public static function get($url, $params, $encoding = 'utf-8', $contentType = 'json', $returnEncoding = '') {
        return self::curlRequest($url, $params, 'GET', $encoding, $contentType, $returnEncoding);
    }

    /**
     * http post 请求 用于提交数据
     * @param                     $url 请求url地址
     * @param array|object|string $params 请求参数
     * @param string $encoding 提交数据编码
     * @param string $contentType 请求数据的内容格式
     * @param string $returnEncoding 返回数据的编码
     * @param array $headMsg 请求头是否添加数据
     * @return string 返回响应内容
     */
    public static function post($url, $params, $encoding = 'utf-8', $contentType = 'json', $returnEncoding = '', $headMsg = null) {
        return self::curlRequest($url, $params, 'POST', $encoding, $contentType, $returnEncoding, $headMsg);
    }

    /**
     * 使用curl组件发起http请求
     * @param                     $url 请求地址
     * @param array|object|string $params
     * @param string $method
     * @param string $encoding
     * @param string $contentType
     * @param string $returnEncoding
     * @param array $headMsg
     * @return mixed|string
     */
    public static function curlRequest($url, $params, $method = 'POST', $encoding = 'utf-8', $contentType = 'json', $returnEncoding = '', $headMsg = null) {
        $SSL      = substr($url, 0, 8) == "https://" ? true : false;
        $ch       = self::_initCurl($url, $params, $method, $encoding, $contentType, $SSL, $headMsg);
        $ret      = curl_exec($ch);
        $errorMsg = curl_error($ch);  //返回字符串报错信息

        curl_close($ch);
        if (!empty($returnEncoding) && $returnEncoding !== $encoding)
            $ret = iconv($encoding, $returnEncoding . '//IGNORE', $ret);
        return $ret ? $ret : $errorMsg;
    }

    /**
     * 设置curl对象属性，并返回curl对象
     * @param string $url 请求url地址
     * @param array|object|string $params 请求参数
     * @param string $method 请求方法名
     * @param string $encoding 请求数据的编码
     * @param string $contentType 请求数据的内容格式
     * @param bool $SSL 是否ssl
     * @param array $headMsg 请求头是否添加数据
     * @return resource 返回curl资源
     */
    private static function _initCurl($url, $params, $method = 'POST', $encoding = 'utf-8', $contentType = 'json', $SSL = false, $headMsg = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设定是否返回的数据,1为是，默认值0（表示不返回数据给调用方）
        curl_setopt($curl, CURLOPT_TIMEOUT, self::TIME_OUT);//设置请求超时时间
        //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIME_OUT);//设置连接超时
        if ($SSL) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
        }
        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_URL, $url);//设置请求路径
            $contentType = strtolower($contentType);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');//post提交方式
            $httpHead = [
                "Content-Type:application/{$contentType}; charset={$encoding}",
                //'Cookie:XDEBUG_SESSION=PHPSTORM',
                //'Connection:close\r\n\r\n'
            ];
            if (is_array($headMsg) && !empty($headMsg)) {
                foreach ($headMsg as $key => $value) {
                    array_push($httpHead, $key . ':' . $value);
                }
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHead);
            if (is_string($params))
                $postedData = $params;
            else if (is_array($params) || is_object($params)) {
                if ($contentType === 'x-www-form-urlencoded')
                    $postedData = self::concatParams($params);//url编码
                else if ($contentType == 'json')
                    $postedData = json_encode($params, JSON_UNESCAPED_UNICODE);
                else
                    $postedData = '';
            } else
                $postedData = '';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postedData);//设置参数
        } elseif ($method === 'GET') {
            $url = $url . '?' . self::concatParams($params);//url编码
            curl_setopt($curl, CURLOPT_URL, $url);//设置请求路径
            //curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        return $curl;
    }
}