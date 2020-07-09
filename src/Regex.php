<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace  metal\helper;

/**
 * 正则工具类
 *
 * @package  metal\helper
 */
final class Regex{

	/**
	 * 验证用户名
	 *
	 * @param string $value 验证的值
	 * @param int    $minLen 最小长度
	 * @param int    $maxLen 最大长度
	 * @param string $type 验证类型，默认‘ALL’,EN.验证英文,CN.验证中文，ALL.验证中文和英文
	 * @return bool
	 */
	public static function isUsername($value, $minLen = 2, $maxLen = 48, $type = 'ALL'){
		if(empty ($value)) return false;

		switch($type){
			case 'EN' :
				$match = '/^[_\w\d]{'.$minLen.','.$maxLen.'}$/iu';
				break;
			case 'CN' :
				$match = '/^[_\x{4e00}-\x{9fa5}\d]{'.$minLen.','.$maxLen.'}$/iu';
				break;
			default :
				$match = '/^[_\w\d\x{4e00}-\x{9fa5}]{'.$minLen.','.$maxLen.'}$/iu';
		}

		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证密码
	 *
	 * @param string $value 验证的值
	 * @param int    $minLen 最小长度
	 * @param int    $maxLen 最大长度
	 * @return bool
	 */
	public static function isPassword($value, $minLen = 6, $maxLen = 16){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{'.$minLen.','.$maxLen.'}$/';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证eamil
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function isEmail($value){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证电话号码
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function isTelephone($value){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/^0[0-9]{2,3}[-]?\d{7,8}$/';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证手机
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function isMobile($value){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/^[(86)|0]?(1\d{10})$/';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证邮政编码
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function isPostCode($value){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/\d{6}/';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证IP
	 *
	 * @param string $value 验证的值
	 * @return boolean
	 */
	public static function isIp($value){
		$value = trim($value);
		if(empty ($value)) return false;

		$match = '/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])'.
			'\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)'.
			'\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)'.
			'\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/';

		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证身份证号码
	 *
	 * @param string $value 验证的值
	 * @return boolean
	 */
	public static function isIDCard($value){
		$value = trim($value);
		if(empty ($value)) return false;
		elseif(strlen($value) > 18) return false;

		$match = '/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i';

		return preg_match($match, $value) !== 0;
	}

	/**
	 * 验证URL
	 *
	 * @param string $value 验证的值
	 * @return boolean
	 */
	public static function isUrl($value){
		$value = strtolower(trim($value));
		if(empty ($value)) return false;
		$match = '/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\d\w-.\/?%&=]*)?$/';
		return preg_match($match, $value) !== 0;
	}

	/**
	 * 是否有数字
	 * 说明:如果字符串中含有非法字符返回假，没有返回真
	 *
	 * @param string $value 验证的值
	 * @return int
	 */
	public static function hasNumber($value){
		return preg_match("/[0-9]/", $value) != false;
	}

	/**
	 * 是否含有英文
	 * 说明:如果字符串中含有非法字符返回假，没有返回真
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function hasEnglish($value){
		return preg_match("/[a-zA-Z]/", $value) != false;
	}

	/**
	 * 是否有中文
	 * 说明:如果字符串中含有非法字符返回假，没有返回真
	 *
	 * @param string $value 验证的值
	 * @return bool
	 */
	public static function hasChinese($value){
		return preg_match("/[\x7f-\xff]/", $value) != false;
	}
}
