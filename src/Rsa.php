<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;
use Exception;

class Rsa
{
    /**
     * 生成签名
     * generateSignature
     * @auth King
     *
     * @param string $data
     * @param string $private_key
     *
     * @return string
     * @throws Exception
     */
    public static function generateSignature($data, $private_key)
    {
        $private_key_resource = openssl_pkey_get_private($private_key);
        if (!$private_key_resource) {
            throw new Exception('私钥格式错误');
        }

        openssl_sign($data, $sign, $private_key_resource, OPENSSL_ALGO_SHA256);

        openssl_free_key($private_key_resource);

        return base64_encode($sign);
    }

    /**
     * 验证签名
     * verifySignature
     * @auth King
     *
     * @param string $data
     * @param string $signature
     * @param string $public_key
     *
     * @return bool
     * @throws Exception
     */
    public static function verifySignature($data, $signature, $public_key)
    {
        $public_key_resource = openssl_pkey_get_public($public_key);
        if (!$public_key_resource) {
            throw new Exception('公钥格式错误');
        }

        $result = (bool)openssl_verify($data, base64_decode($signature), $public_key_resource, OPENSSL_ALGO_SHA256);

        openssl_free_key($public_key_resource);

        return $result;
    }

    /**
     * 私钥加密数据
     * privateKeyEncrypt
     * @auth King
     *
     * @param string $data
     * @param string $private_key
     *
     * @return string
     * @throws Exception
     */
    public static function privateKeyEncrypt($data, $private_key)
    {
        $private_key_resource = openssl_pkey_get_private($private_key);
        if (!$private_key_resource) {
            throw new Exception('私钥格式错误');
        }

        openssl_private_encrypt($data, $decrypted, $private_key_resource);

        openssl_free_key($private_key_resource);

        return base64_encode($decrypted);
    }

    /**
     * 私钥解密数据
     *
     * @param $data
     * @param $private_key
     *
     * @return mixed
     * @throws Exception
     * @author King
     *
     */
    public static function privateKeyDecrypt($data, $private_key)
    {
        $private_key_resource = openssl_pkey_get_private($private_key);
        if (!$private_key_resource) {
            throw new Exception('私钥格式错误');
        }

        openssl_private_decrypt(base64_decode($data), $decrypted, $private_key_resource);

        openssl_free_key($private_key_resource);

        return $decrypted;
    }

    /**
     * 公钥加密数据
     *
     * @param string $data 要加密的数据
     * @param string $public_key 公钥
     *
     * @return string
     * @throws Exception
     * @author King
     */
    public static function publicKeyEncrypt($data, $public_key)
    {
        $public_key_resource = openssl_pkey_get_public($public_key);
        if (!$public_key_resource) {
            throw new Exception('公钥格式错误');
        }

        openssl_public_encrypt($data, $decrypted, $public_key_resource);

        openssl_free_key($public_key_resource);

        return base64_encode($decrypted);
    }

    /**
     * 公钥解密数据
     * privateKeyEncrypt
     * @auth King
     *
     * @param string $data
     * @param string $public_key
     *
     * @return string
     * @throws Exception
     */
    public static function publicKeyDecrypt($data, $public_key)
    {
        $public_key_resource = openssl_pkey_get_public($public_key);
        if (!$public_key_resource) {
            throw new Exception('公钥格式错误');
        }

        openssl_public_decrypt(base64_decode($data), $decrypted, $public_key_resource);

        openssl_free_key($public_key_resource);

        return $decrypted;
    }
}