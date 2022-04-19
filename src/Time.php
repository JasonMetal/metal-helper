<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

final class Time {

    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today() {
        list($y, $m, $d) = explode('-', date('Y-m-d'));
        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y),
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday() {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y')),
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week() {
        list($y, $m, $d, $w) = explode('-', date('Y-m-d-w'));
        if ($w == 0) $w = 7; //修正周日的问题
        return [
            mktime(0, 0, 0, $m, $d - $w + 1, $y), mktime(23, 59, 59, $m, $d - $w + 7, $y),
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek() {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp))),
            strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month() {
        list($y, $m, $t) = explode('-', date('Y-m-t'));
        return [
            mktime(0, 0, 0, $m, 1, $y),
            mktime(23, 59, 59, $m, $t, $y),
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth() {
        $y     = date('Y');
        $m     = date('m');
        $begin = mktime(0, 0, 0, $m - 1, 1, $y);
        $end   = mktime(23, 59, 59, $m - 1, date('t', $begin), $y);

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year() {
        $y = date('Y');
        return [
            mktime(0, 0, 0, 1, 1, $y),
            mktime(23, 59, 59, 12, 31, $y),
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear() {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year),
        ];
    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow($day = 1, $now = true) {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }

        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end,
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1) {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1) {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function daysToSecond($day = 1) {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1) {
        return self::daysToSecond() * 7 * $week;
    }

    /**
     * 获取毫秒级别的时间戳
     */
    public static function getMillisecond() {
        $time  = explode(" ", microtime());
        $time  = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time  = $time2[0];
        return $time;
    }

    /**
     * 获取相对时间
     *
     * @param int $timeStamp
     * @return string
     */
    public static function formatRelative($timeStamp) {
        $currentTime = time();

        // 判断传入时间戳是否早于当前时间戳
        $isEarly = $timeStamp <= $currentTime;

        // 获取两个时间戳差值
        $diff = abs($currentTime - $timeStamp);

        $dirStr = $isEarly ? '前' : '后';

        if ($diff < 60) { // 一分钟之内
            $resStr = $diff . '秒' . $dirStr;
        } elseif ($diff >= 60 && $diff < 3600) { // 多于59秒，少于等于59分钟59秒
            $resStr = floor($diff / 60) . '分钟' . $dirStr;
        } elseif ($diff >= 3600 && $diff < 86400) { // 多于59分钟59秒，少于等于23小时59分钟59秒
            $resStr = floor($diff / 3600) . '小时' . $dirStr;
        } elseif ($diff >= 86400 && $diff < 2623860) { // 多于23小时59分钟59秒，少于等于29天59分钟59秒
            $resStr = floor($diff / 86400) . '天' . $dirStr;
        } elseif ($diff >= 2623860 && $diff <= 31567860 && $isEarly) { // 多于29天59分钟59秒，少于364天23小时59分钟59秒，且传入的时间戳早于当前
            $resStr = date('m-d H:i', $timeStamp);
        } else {
            $resStr = date('Y-m-d', $timeStamp);
        }

        return $resStr;
    }

    /**
     * 范围日期转换时间戳
     *
     * @param string $rangeDatetime
     * @param int $maxRange 最大时间间隔
     * @param string $delimiter
     * @return array
     */
    public static function parseRange($rangeDatetime, $maxRange = 0, $delimiter = ' - ') {
        $rangeDatetime    = explode($delimiter, $rangeDatetime, 2);
        $rangeDatetime[0] = strtotime($rangeDatetime[0]);
        $rangeDatetime[1] = isset($rangeDatetime[1]) ? strtotime($rangeDatetime[1]) : time();

        // 如果结束时间小于或等于开始时间 直接返回null
        if ($rangeDatetime[1] < $rangeDatetime[0]) {
            return null;
        }

        // 如果大于最大时间间隔 则用结束时间减去最大时间间隔获得开始时间
        if ($maxRange > 0 && $rangeDatetime[1] - $rangeDatetime[0] > $maxRange) {
            $rangeDatetime[0] = $rangeDatetime[1] - $maxRange;
        }

        return $rangeDatetime;
    }


    /**
     * 程序开始时间
     * @start time
     * @return string
     */
    public static function proStartTime() {
        global $startTime;
        $mtime1    = explode(" ", microtime());
        $startTime = $mtime1[1] + $mtime1[0];
    }

    /**
     * 程序结束时间
     * @End time
     * @return string process time:
     */
    public static function proEndTime() {
        global $startTime, $set;
        $mtime2    = explode(" ", microtime());
        $endtime   = $mtime2[1] + $mtime2[0];
        $totaltime = ($endtime - $startTime);
        $totaltime = number_format($totaltime, 7);
        return $totaltime;
    }

    /**
     * 获取十三位时间戳
     * @param string $rangeDatetime
     * @return string 1501234567890
     */
    public static function get13TimeStamp() {
        list($t1, $t2)
            = explode(' ', microtime());
        return $t2 . ceil($t1 * 1000);
    }


    /**从表查询的数组中，格式化输出时间
     * @param $list
     * @param string $field_name 字段
     * @return array|string
     * @time : 2018/6/11 15:01
     */
    public static function formatTime(&$list, $field_name = '') {

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if ($value[$field_name] == 0) {
                    $list[$key][$field_name] = intval(0);
                } else {
                    $list[$key][$field_name] = date('Y-m-d H:i:s', $value[$field_name]);
                }
            }
        }
        return $list;
    }

}
