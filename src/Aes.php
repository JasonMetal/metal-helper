<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;
use Exception;

class Aes
{
    /**
     * var string $method 加解密方法，可通过openssl_get_cipher_methods()获得
     */
    protected $method;

    /**
     * var string $key 加解密的密钥
     */
    protected $key;

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv;

    /**
     * var string $options
     */
    protected $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;


    /**
     * 构造函数
     *
     * @param string $key 密钥
     * @param string $method 加密方式
     * @param string $iv iv向量
     * @param int $options
     *
     * @throws Exception
     */
    public function __construct($key, $method = 'aes-256-ecb', $iv = '', $options = 0)
    {
        if (empty($key)) {
            throw new Exception('加解密的密钥不能为空');
        }
        $this->key = $key;

        if (!in_array(strtolower($method), openssl_get_cipher_methods())) {
            throw new Exception('加密方式选择错误');
        }
        $this->method = $method;

        $this->iv = $iv;

        $this->options = $options ? $options : 0;
    }

    /**
     * 加密方法，对数据进行加密，返回加密后的数据
     *
     * @param string $data 要加密的数据
     *
     * @return string
     * @author King
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据
     *
     * @param string $data 要解密的数据
     *
     * @return string
     * @author King
     */
    public function decrypt($data)
    {
        return openssl_decrypt($data, $this->method, $this->key, $this->options, $this->iv);
    }

}
