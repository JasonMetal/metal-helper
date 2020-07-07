<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license       http://www.apache.org/licenses/LICENSE-2.0
 * @author        <657306123@qq.com> LXSEA
 */

namespace  metal\helper;

/**
 * 函数工具类
 *
 * @package  metal\helper
 */
final class Func{

	/**
	 * 中间件
	 *
	 * @param callable $bindFunc
	 * @param array    $functions
	 * @return mixed
	 */
	public static function middleware(callable $bindFunc, $functions){
		return function($context) use (&$functions, &$bindFunc){
			$index = 0;
			$next = function() use (&$functions, &$index, &$bindFunc, &$context, &$next){
				if(isset($functions[$index])){
					$function = $functions[$index++];
					return call_user_func_array($function, [&$context, $next]);
				}else{
					return call_user_func_array($bindFunc, [&$context, $next]);
				}
			};
			return $next();
		};
	}
}
