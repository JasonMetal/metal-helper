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
 * 各地图Aself::PI坐标系统比较与转换;
 * WGS84坐标系：即地球坐标系，国际上通用的坐标系。设备一般包含GPS芯片或者北斗芯片获取的经纬度为WGS84地理坐标系,
 * 谷歌地图采用的是WGS84地理坐标系（中国范围除外）;
 * GCJ02坐标系：即火星坐标系，是由中国国家测绘局制订的地理信息系统的坐标系统。由WGS84坐标系经加密后的坐标系。
 * 谷歌中国地图和搜搜中国地图采用的是GCJ02地理坐标系; BD09坐标系：即百度坐标系，GCJ02坐标系经加密后的坐标系;
 * 搜狗坐标系、图吧坐标系等，估计也是在GCJ02基础上加密而成的。 chenhua
 */
final class Position{

	const  BAIDU_LBS_TYPE = "bd09ll";

	const  PI = 3.1415926535897932384626;

	const  A = 6378245.0;

	const  EE = 0.00669342162296594323;

	/**
	 * 84 to 火星坐标系 (GCJ-02) World Geodetic System ==> Mars Geodetic System
	 *
	 * @param float $lat
	 * @param float $lon
	 * @return array
	 */
	public static function gps84ToGcj02($lat, $lon){
		if(self::outOfChina($lat, $lon)){
			return null;
		}
		$dLat = self::transformLat($lon - 105.0, $lat - 35.0);
		$dLon = self::transformLon($lon - 105.0, $lat - 35.0);
		$radLat = $lat / 180.0 * self::PI;
		$magic = sin($radLat);
		$magic = 1 - self::EE * $magic * $magic;
		$sqrtMagic = sqrt($magic);
		$dLat = ($dLat * 180.0) / ((self::A * (1 - self::EE)) / ($magic * $sqrtMagic) * self::PI);
		$dLon = ($dLon * 180.0) / (self::A / $sqrtMagic * cos($radLat) * self::PI);
		$mgLat = $lat + $dLat;
		$mgLon = $lon + $dLon;
		return [$mgLat, $mgLon];
	}

	/**
	 * 是否在中国
	 *
	 * @param double $lat
	 * @param double $lon
	 * @return bool
	 */
	public static function outOfChina($lat, $lon){
		if($lon < 72.004 || $lon > 137.8347)
			return true;
		if($lat < 0.8293 || $lat > 55.8271)
			return true;
		return false;
	}

	/**
	 * 转换维度
	 *
	 * @param float $x
	 * @param float $y
	 * @return float
	 */
	public static function transformLat($x, $y){
		$ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y
			+ 0.2 * sqrt(abs($x));
		$ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
		$ret += (20.0 * sin($y * self::PI) + 40.0 * sin($y / 3.0 * self::PI)) * 2.0 / 3.0;
		$ret += (160.0 * sin($y / 12.0 * self::PI) + 320 * sin($y * self::PI / 30.0)) * 2.0 / 3.0;
		return $ret;
	}

	/**
	 * 转换经度
	 *
	 * @param float $x
	 * @param float $y
	 * @return float
	 */
	public static function transformLon($x, $y){
		$ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1
			* sqrt(abs($x));
		$ret += (20.0 * sin(6.0 * $x * self::PI) + 20.0 * sin(2.0 * $x * self::PI)) * 2.0 / 3.0;
		$ret += (20.0 * sin($x * self::PI) + 40.0 * sin($x / 3.0 * self::PI)) * 2.0 / 3.0;
		$ret += (150.0 * sin($x / 12.0 * self::PI) + 300.0 * sin($x / 30.0
					* self::PI)) * 2.0 / 3.0;
		return $ret;
	}

	/**
	 * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换算法 将 GCJ-02 坐标转换成 BD-09 坐标
	 *
	 * @param float $gg_lat
	 * @param float $gg_lon
	 * @return array
	 */
	public static function gcj02ToBD09($gg_lat, $gg_lon){
		$x = $gg_lon;
		$y = $gg_lat;
		$z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * self::PI);
		$theta = atan2($y, $x) + 0.000003 * cos($x * self::PI);
		$bd_lon = $z * cos($theta) + 0.0065;
		$bd_lat = $z * sin($theta) + 0.006;
		return [$bd_lat, $bd_lon];
	}

	/**
	 * (BD-09)-->84
	 *
	 * @param double $bd_lat
	 * @param double $bd_lon
	 * @return array
	 */
	public static function bd09ToGps84($bd_lat, $bd_lon){
		$gcj02 = self::bd09ToGcj02($bd_lat, $bd_lon);
		return self::gcjToGps84($gcj02[0], $gcj02[1]);
	}

	/**
	 * * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换算法 * * 将 BD-09 坐标转换成GCJ-02 坐标 * *
	 *
	 * @param float $bd_lat
	 * @param float $bd_lon
	 * @return array
	 */
	public static function bd09ToGcj02($bd_lat, $bd_lon){
		$x = $bd_lon - 0.0065;
		$y = $bd_lat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::PI);
		$theta = atan2($y, $x) - 0.000003 * cos($x * self::PI);
		$gg_lon = $z * cos($theta);
		$gg_lat = $z * sin($theta);
		return [$gg_lat, $gg_lon];
	}

	/**
	 * 火星坐标系 (GCJ-02) to 84
	 *
	 * @param float $lat
	 * @param float $lon
	 * @return array
	 **/
	public static function gcjToGps84($lat, $lon){
		$gps = self::transform($lat, $lon);
		$latitude = $lat * 2 - $gps[0];
		$longitude = $lon * 2 - $gps[1];
		return [$latitude, $longitude];
	}

	/**
	 * 变换坐标
	 *
	 * @param double $lat
	 * @param double $lon
	 * @return array
	 */
	public static function transform($lat, $lon){
		if(self::outOfChina($lat, $lon)){
			return [$lat, $lon];
		}
		$dLat = self::transformLat($lon - 105.0, $lat - 35.0);
		$dLon = self::transformLon($lon - 105.0, $lat - 35.0);
		$radLat = $lat / 180.0 * self::PI;
		$magic = sin($radLat);
		$magic = 1 - self::EE * $magic * $magic;
		$sqrtMagic = sqrt($magic);
		$dLat = ($dLat * 180.0) / ((self::A * (1 - self::EE)) / ($magic * $sqrtMagic) * self::PI);
		$dLon = ($dLon * 180.0) / (self::A / $sqrtMagic * cos($radLat) * self::PI);
		$mgLat = $lat + $dLat;
		$mgLon = $lon + $dLon;
		return [$mgLat, $mgLon];
	}

}
