<?php
/**
 * 
 *
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author BD<657306123@qq.com>
 */

namespace  metal\helper;

/**
 * 数字工具类
 *
 * @package  metal\helper
 */
final class Num{

	/**
	 * 保留小数点两位
	 *
	 * @param float $n 要格式化的浮点数
	 * @param int   $y 要保留的小说点位数
	 * @return float
	 */
	public static function formatFloat($n, $y = 2){ // 保留小数点两位
		$str = "%.".($y * 2)."f";
		return floatval(substr(sprintf($str, $n), 0, -2));
	}

	/**
	 * 保留小数点两位
	 *
	 * @param float $n 要格式化的浮点数
	 * @param int   $y 要保留的小说点位数
	 * @return float
	 */
	public static function formatFloat2($n, $y = 2){
		return round($n, $y, PHP_ROUND_HALF_DOWN);
	}

	/**
	 * 格式化字节大小
	 *
	 * @param  number $size 字节数
	 * @param  string $delimiter 数字和单位分隔符
	 * @return string            格式化后的带单位的大小
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public static function formatBytes($size, $delimiter = ''){
		$units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		for($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
		return round($size, 2).$delimiter.$units[$i];
	}

	/**
	 * 人性化数字
	 *
	 * @param int $num
	 * @return string
	 */
	public static function formatSimple($num){
		if($num < 1000){
			return $num;
		}

		if($num < 10000){
			return round($num / 1000, 2)."千";
		}

		if($num < 100000000){
			return round($num / 10000, 2)."万";
		}

		return round($num / 100000000, 2)."亿";
	}

	/**
	 * 计算两点地理坐标之间的距离
	 *
	 * @param  float $longitude1 起点经度
	 * @param  float $latitude1 起点纬度
	 * @param  float $longitude2 终点经度
	 * @param  float $latitude2 终点纬度
	 * @param  int   $unit 单位 1:米 2:公里
	 * @param  int   $decimal 精度 保留小数位数
	 * @return float
	 */
	public static function calcDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2){
		$EARTH_RADIUS = 6370.996; // 地球半径系数
		$PI = 3.1415926;

		$radLat1 = $latitude1 * $PI / 180.0;
		$radLat2 = $latitude2 * $PI / 180.0;

		$radLng1 = $longitude1 * $PI / 180.0;
		$radLng2 = $longitude2 * $PI / 180.0;

		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;

		$distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
		$distance = $distance * $EARTH_RADIUS * 1000;

		if($unit == 2){
			$distance = $distance / 1000;
		}

		return round($distance, $decimal);
	}
}
