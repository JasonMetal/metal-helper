<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */


namespace  metal\helper;

/**
 * 系统相关操作工具类
 *
 * @package  metal\helper
 */
final class Server{

	/**
	 * 获取客户的IP地址
	 *
	 * @return string
	 */
	public static function getRemoteIp(){
		if(isset($_SERVER ["HTTP_X_FORWARDED_FOR"])){
			$ip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		}elseif(isset($_SERVER ["HTTP_CLIENT_IP"])){
			$ip = $_SERVER ["HTTP_CLIENT_IP"];
		}elseif(isset($_SERVER ["REMOTE_ADDR"])){
			$ip = $_SERVER ["REMOTE_ADDR"];
		}else{
			$ip = "0.0.0.0";
		}

		return $ip;
	}

	/**
	 * 获取客户端端口号
	 *
	 * @return int
	 */
	public static function getRemotePort(){
		$port = 0;
		if(isset($_SERVER ["REMOTE_PORT"])){
			$port = $_SERVER ["REMOTE_PORT"];
		}elseif(isset($_COOKIE ["REMOTE_PORT"])){
			$port = $_COOKIE ["REMOTE_PORT"];
		}elseif(isset($_POST ["REMOTE_PORT"])){
			$port = $_POST ["REMOTE_PORT"];
		}elseif(isset($_GET ["REMOTE_PORT"])){
			$port = $_GET ["REMOTE_PORT"];
		}

		return $port;
	}

	/**
	 * 获取主机名称
	 *
	 * @return string
	 */
    public static function getServerName(){
		return $_SERVER ['SERVER_NAME'];
	}

	/**
	 * 获取当前访问的文件
	 *
	 * @return string
	 */
	public static function getExecuteFile(){
		$urls = explode('/', strip_tags($_SERVER ['REQUEST_URI']), 2);
		return count($urls) > 1 ? $urls [1] : '';
	}

	/**
	 * 获取所有请求头信息
	 *
	 * @return array
	 */
	public static function getAllHeader(){
		$headers = [];
		foreach($_SERVER as $key => $value){
			if('HTTP_' == substr($key, 0, 5)){
				$headers [str_replace('_', '-', substr($key, 5))] = $value;
			}
		}
		if(isset ($_SERVER ['PHP_AUTH_DIGEST'])){
			$headers ['AUTHORIZATION'] = $_SERVER ['PHP_AUTH_DIGEST'];
		}elseif(isset ($_SERVER ['PHP_AUTH_USER']) && isset ($_SERVER ['PHP_AUTH_PW'])){
			$headers ['AUTHORIZATION'] = base64_encode($_SERVER ['PHP_AUTH_USER'].':'.$_SERVER ['PHP_AUTH_PW']);
		}
		if(isset ($_SERVER ['CONTENT_LENGTH'])){
			$headers ['CONTENT-LENGTH'] = $_SERVER ['CONTENT_LENGTH'];
		}
		if(isset ($_SERVER ['CONTENT_TYPE'])){
			$headers ['CONTENT-TYPE'] = $_SERVER ['CONTENT_TYPE'];
		}

		return $headers;
	}

	/**
	 * 获取终端名称
	 *
	 * @param bool $isVersion 是否要返回版本号
	 * @return string
	 */
	public static function getClientName($isVersion = true){
		// 获取客户端版本信息
		$getVersion = function($str, $checkname){
			$pos = strpos($str, $checkname);
			$len = strpos($str, ';', $pos);
			$len = $len ? $len - $pos : strlen($str) - $pos;
			return substr($str, $pos, $len);
		};

		$info = self::getClientInfo();
		if(strpos($info ['info_str'], 'windows phone') !== false){
			if(!$isVersion) return "windows phone";
			return $getVersion($info ['info_str'], 'windows phone');
		}else{
			if(strpos($info ['info_str'], 'windows') !== false){
				if(!$isVersion) return "windows";
				return $getVersion($info ['info_str'], 'windows');
			}elseif(strpos($info ['info_str'], 'android') !== false){
				if(!$isVersion) return "android";
				return $getVersion($info ['info_str'], 'android');
			}elseif(strpos($info ['info_str'], 'iphone') !== false){
				if(!$isVersion) return "iphone";
				return $getVersion($info ['info_str'], 'iphone');
			}elseif(strpos($info ['info_str'], 'mac os') !== false){
				if(!$isVersion) return "mac os";
				return $getVersion($info ['info_str'], 'mac os');
			}elseif(is_array($info ['info'])){
				if($isVersion == false){
					$info ['info'] = explode(" ", $info ['info'] [0]);
				}
				return $info ['info'] [0];
			}else{
				return "other";
			}
		}
	}

	/**
	 * 获取终端信息
	 *
	 * @return array
	 */
	public static function getClientInfo(){
		$info = [];
		$user_agent = strtolower($_SERVER ['HTTP_USER_AGENT']);
		$firstSpilt = strpos($user_agent, ')');
		$user_agent2 = substr($user_agent, 0, $firstSpilt);
		$user_agents = explode(" ", $user_agent2, 2);
		$mozilla = explode("/", $user_agents [0], 2);
		$info [$mozilla [0]] = $mozilla [1];
		$user_agent2 = substr($user_agents [1], 1);
		$info ['info'] = explode("; ", $user_agent2);
		$info ['info_str'] = $user_agent2;

		// applewebkit/537.36
		$user_agent2 = substr($user_agent, $firstSpilt + 2, strlen($user_agent) - $firstSpilt);
		$user_agent2 = preg_replace('/(\(.*\))\s/', "", $user_agent2);
		$user_agents = explode(" ", $user_agent2);
		$len = count($user_agents);
		for($i = 0; $i < $len; $i++){
			$temps = explode("/", $user_agents [$i], 2);
			$info [$temps [0]] = $temps [1];
		}

		return $info;
	}

	/**
	 * 获取序列化参数
	 *
	 * @param bool $isExportStyle
	 * @return string
	 */
	public static function serializeParams($isExportStyle = true){
		if($isExportStyle){
			return var_export([
				"GET"     => $_GET,
				"POST"    => $_POST,
				"COOKIE"  => $_COOKIE,
				"SESSION" => $_SESSION,
				"SERVER"  => $_SERVER,
			], true);
		}
		return "[GET=".http_build_query($_GET)."],"
			."[POST=".http_build_query($_POST, false)."],".
			"[COOKIE=".http_build_query($_COOKIE, false)."],".
			"[SESSION=".http_build_query($_SESSION, false)."],".
			"[SERVER=".http_build_query($_SERVER, false)."]";
	}

	/**
	 * 是否移动端访问访问
	 *
	 * @return bool
	 */
	public static function isMobileVisit(){
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if(isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
			return true;
		}
		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if(isset ($_SERVER['HTTP_VIA'])){
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		}
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if(isset ($_SERVER['HTTP_USER_AGENT'])){
			$clientkeywords = [
				'nokia', 'sony', 'ericsson', 'mot',
				'samsung', 'htc', 'sgh', 'lg',
				'sharp', 'sie-', 'philips', 'panasonic',
				'alcatel', 'lenovo', 'iphone', 'ipod',
				'blackberry', 'meizu', 'android', 'netfront',
				'symbian', 'ucweb', 'windowsce', 'palm',
				'operamini', 'operamobi', 'openwave', 'nexusone',
				'cldc', 'midp', 'wap', 'mobile',
			];
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if(preg_match("/(".implode('|', $clientkeywords).")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
				return true;
			}
		}
		// 协议法，因为有可能不准确，放到最后判断
		if(isset ($_SERVER['HTTP_ACCEPT'])){
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false)
				&& (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false
					|| (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
				return true;
			}
		}

		return false;
	}

	/**
	 * 调用客户端回掉函数
	 *
	 * @param $callback
	 * @param $param
	 */
	public static function flushScriptCall($callback, $param = ''){
		if(is_string($param)){
			$param = '"'.addslashes($param).'"';
		}else{
			if(is_array($param)){
				if(key($param) == 0){
					$tmpStr = '';
					$len = count($param);
					for($i = 0; $i < $len; $i++){
						if($i != 0) $tmpStr .= ",";
						$tmpStr .= '"'.addslashes($param).'"';
					}
					$param = $tmpStr;
				}else{
					$param = json_encode($param);
				}
			}
		}
		self::flushScript("{$callback} ( ".json_encode($param)." )");
	}

	/**
	 * 发送到客户端script
	 *
	 * @param string $script
	 */
	public static function flushScript($script){
		echo "<script type=\"text/javascript\">{$script}</script>";
		flush();
		ob_flush();
	}

	/**
	 * URL重定向
	 *
	 * @param string  $url 重定向的URL地址
	 * @param integer $time 重定向的等待时间（秒）
	 * @param string  $msg 重定向前的提示信息
	 * @return void
	 */
	public static function redirect($url, $time = 0, $msg = ''){
		//多行URL地址支持
		$url = str_replace(["\n", "\r"], '', $url);
		if(empty($msg)){
			$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
		}

		if(!headers_sent()){
			// redirect
			if(0 === $time){
				header('Location: '.$url);
			}else{
				header("refresh:{$time};url={$url}");
				echo($msg);
			}
			exit();
		}else{
			$str = "<meta http-equiv=\"Refresh\" content=\"{$time};URL={$url}\">";
			if(0 != $time){
				$str .= $msg;
			}
			exit($str);
		}
	}
}
