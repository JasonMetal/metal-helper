<?php


namespace metal\helper;

class Curl
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
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

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
     * @param int $timeout 超时时间
     * @param array $header Header头
     *
     * @return mixed
     * 
     *
     */
    public static function get($url, $timeout, $header = [])
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
        return self::request($url, 'POST', $data, $timeout, $header);
    }
}