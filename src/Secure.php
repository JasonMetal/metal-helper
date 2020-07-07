<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author BD<657306123@qq.com>
 */

namespace  metal\helper;

/**
 * 安全相关工具类
 *
 * @package  metal\helper
 */
final class Secure{

	/**
	 * 获取签名证书ID
	 *
	 * @param string $cert_path 签名证书路径
	 * @param string $cert_pwd 签名证书密码
	 * @return mixed
	 */
	public static function getSignCertId($cert_path, $cert_pwd){
		$pkcs12certdata = file_get_contents($cert_path);
		openssl_pkcs12_read($pkcs12certdata, $certs, $cert_pwd);
		$x509data = $certs ['cert'];
		openssl_x509_read($x509data);
		$certdata = openssl_x509_parse($x509data);
		$cert_id = $certdata ['serialNumber'];
		return $cert_id;
	}

	/**
	 * 返回(签名)证书私钥
	 *
	 * @param $cert_path
	 * @param $cert_pwd
	 * @return mixed
	 */
	public static function getPrivateKey($cert_path, $cert_pwd){
		$pkcs12 = file_get_contents($cert_path);
		openssl_pkcs12_read($pkcs12, $certs, $cert_pwd);
		return $certs ['pkey'];
	}

	/**
	 * 根据证书ID 加载 证书
	 *
	 * @param $certId
	 * @param $cert_dir
	 * @return string
	 */
	public static function getPulbicKeyByCertId($certId, $cert_dir){
		$handle = opendir($cert_dir);
		if(!$handle) return null;

		while($file = readdir($handle)){
			clearstatcache();
			$filePath = $cert_dir.'/'.$file;
			if(is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) == 'cer'){
				if(self::getCertIdByCerPath($filePath) == $certId){
					closedir($handle);
					return file_get_contents($filePath);
				}
			}
		}

		closedir($handle);
		return null;
	}

	/**
	 * 取证书ID(.cer)
	 *
	 * @param string $cert_path 证书路径
	 * @return string
	 */
	public static function getCertIdByCerPath($cert_path){
		$x509data = file_get_contents($cert_path);
		openssl_x509_read($x509data);
		$certdata = openssl_x509_parse($x509data);
		$cert_id = $certdata ['serialNumber'];
		return $cert_id;
	}

	/**
	 * 签名字符串
	 *
	 * @param string $content 要加密的内容
	 * @param string $public_key 证书公钥
	 * @return string
	 */
	public static function rsaSign($content, $public_key){
		$pkeyid = openssl_get_privatekey($public_key);
		openssl_sign($content, $sign, $pkeyid);
		openssl_free_key($pkeyid);
		$sign = base64_encode($sign);
		return $sign;
	}

	/**
	 * 验证签名
	 *
	 * @param string $content 要解密的内容
	 * @param string $sign 签名字符串
	 * @param string $public_key 证书公钥
	 * @return bool
	 */
	public static function rsaVerify($content, $sign, $public_key){
		$sign = base64_decode($sign);
		$pkeyid = openssl_get_publickey($public_key);
		$verify = '';
		if($pkeyid){
			$verify = openssl_verify($content, $sign, $pkeyid);
			openssl_free_key($pkeyid);
		}
		if($verify){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 系统加密方法
	 *
	 * @param string $data 要加密的字符串
	 * @param string $key 加密密钥
	 * @param int    $expire 过期时间 单位 秒
	 * @return string
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public static function encrypt($data, $key = '', $expire = 0){
		$key = md5($key);
		$data = base64_encode($data);
		$x = 0;
		$len = strlen($data);
		$l = strlen($key);
		$char = '';

		for($i = 0; $i < $len; $i++){
			if($x == $l) $x = 0;
			$char .= substr($key, $x, 1);
			$x++;
		}

		$str = sprintf('%010d', $expire ? $expire + time() : 0);

		for($i = 0; $i < $len; $i++){
			$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
		}
		return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
	}

	/**
	 * 系统解密方法
	 *
	 * @param  string $data 要解密的字符串
	 * @param  string $key 加密密钥
	 * @return string
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public static function decrypt($data, $key = ''){
		$key = md5($key);
		$data = str_replace(array('-', '_'), array('+', '/'), $data);
		$mod4 = strlen($data) % 4;
		if($mod4){
			$data .= substr('====', $mod4);
		}
		$data = base64_decode($data);
		$expire = substr($data, 0, 10);
		$data = substr($data, 10);

		if($expire > 0 && $expire < time()){
			return '';
		}
		$x = 0;
		$len = strlen($data);
		$l = strlen($key);
		$char = $str = '';

		for($i = 0; $i < $len; $i++){
			if($x == $l) $x = 0;
			$char .= substr($key, $x, 1);
			$x++;
		}

		for($i = 0; $i < $len; $i++){
			if(ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))){
				$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			}else{
				$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
		}
		return base64_decode($str);
	}

}
