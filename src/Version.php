<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

/**
 * 版本检测器
 * 当前版本大于新版本 VersionUtil::check( '1.20.63.56' , '1.20.63.55.56' )===1;
 * 当前版本等于新版本 VersionUtil::check( '1.20.63.56' , '1.20.63.056' )===0;
 * 当前版本小于新版本 VersionUtil::check( '1.20.62.56' , '1.20.63.056' )===-1;
 * 当前版本大于新版本 VersionUtil::gt( '1.20.63.56' , '1.20.63.55.56' )===true;
 * 当前版本等于新版本 VersionUtil::eq( '1.20.63.56' , '1.20.63.056' )===true;
 * 当前版本小于新版本 VersionUtil::lt( '1.20.62.56' , '1.20.63.056' )===true;
 *
 * @package  metal\helper
 */
final class Version {

    /**
     * 当前版本大于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function gt($current, $new) {
        return self::check($current, $new) === 1;
    }

    /**
     * 版本检测
     *
     * @param $current
     * @param $new
     * @return int
     */
    public static function check($current, $new) {
        if ($current == $new) return 0;

        $current = explode(".", $current);
        $new     = explode(".", $new);
        foreach ($current as $k => $cur) {
            if (isset($new[$k])) {
                if ($cur < $new[$k]) {
                    return -1;
                } elseif ($cur > $new[$k]) {
                    return 1;
                }
            } else {
                return 1;
            }
        }
        return count($new) > count($current) ? -1 : 0;
    }

    /**
     * 当前版本大于或等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function egt($current, $new) {
        $res = self::check($current, $new);
        return $res === 1 || $res === 0;
    }

    /**
     * 当前版本等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function eq($current, $new) {
        return self::check($current, $new) === 0;
    }

    /**
     * 当前版本小于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function lt($current, $new) {
        return self::check($current, $new) === -1;
    }

    /**
     * 当前版本小于或等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function elt($current, $new) {
        $res = self::check($current, $new);
        return $res === -1 || $res === 0;
    }

    /**
     * 版本转化 eg:3002001 => 3.2.1
     * @param $version
     * @return string
     */
    public static function toVersionCode($version) {
        // eg:3002001 => 3.2.1
        $versionCode = str_pad($version, 9, "0", STR_PAD_LEFT);
        $version1    = ltrim(substr($versionCode, 0, 3), 0) | "0";
        $version2    = ltrim(substr($versionCode, 3, 3), 0) | "0";
        $version3    = ltrim(substr($versionCode, 6, 3), 0) | "0";
        $versionCode = $version1 . "." . $version2 . "." . $version3;
        return $versionCode;
    }

    /**
     * 版本转化 eg:3.2.1 => 3002001
     * @param $version
     * @return string
     */
    public static function toVersionNum($version) {
        // eg:3.2.1 => 3002001
        $versionArr = explode(".", $version);
        $version1   = $versionArr[0];
        $version2   = str_pad($versionArr[1], 3, "0", STR_PAD_LEFT);
        $version3   = str_pad($versionArr[2], 3, "0", STR_PAD_LEFT);
        $versionNum = ltrim($version1 . $version2 . $version3, "0");
        return $versionNum;
    }

    /**
     * 版本比较
     * @param string $v1
     * @param string $v2
     * @return int
     * */
    public static function compare_version($v1, $v2) {
        $v1     = explode('.', $v1);
        $v2     = explode('.', $v2);
        $length = max(count($v1), count($v2));
        while (count($v1) < $length) {
            array_push($v1, 0);
        }
        while (count($v2) < $length) {
            array_push($v2, 0);
        }
        for ($i = 0; $i < $length; $i++) {
            $num1 = (int)$v1[$i];
            $num2 = (int)$v2[$i];
            if ($num1 > $num2) {
                return 1;
            } else if ($num1 < $num2) {
                return -1;
            }
        }
        return 0;
    }
}
