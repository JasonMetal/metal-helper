<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

/**
 * 字符串工具类
 *
 * @package  metal\helper
 */
final class Str
{


     /**
     * 获取变量名
     * 例如 get_variable_name($a, get_defined_vars())
     * @var string
     */
    protected static function get_variable_name(&$var, $scope = null)
    {

        $scope = $scope == null ? $GLOBALS : $scope; // 如果没有范围则在globals中找寻
        $tmp = $var;
        $var  = 'tmp_value_' . mt_rand();
        $name = array_search($var, $scope, true); // 根据值查找变量名称
        $var = $tmp;
        return $name;
    }


    /**
     * 驼峰转下划线缓存
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * 下划线转驼峰(首字母小写) 缓存
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * 检查字符串中是否包含某些字符串
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === static::subString($haystack, -mb_strlen($needle))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string $value 验证的值
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param string $charset 字符编码
     * @return string
     */
    public static function subString($value, $start = 0, $length = null, $charset = null)
    {
        if (function_exists("mb_substr"))
            $slice = mb_substr($value, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $length  = is_null($length) ? $length = 'iconv_strlen($str, $charset)' : $length;
            $charset = is_null($charset) ? $charset = 'ini_get("iconv.internal_encoding")' : $charset;
            $slice   = iconv_substr($value, $start, $length, $charset);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re ['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re ['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re ['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re [$charset], $value, $match);
            $slice = join("", array_slice($match [0], $start, $length));
        }
        return $slice;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 字符串转小写
     *
     * @param string $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param string $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * 获取字符串的长度
     *
     * @param string $value
     * @return int
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * 驼峰转下划线
     *
     * @param string $value
     * @param string $delimiter
     * @param bool $isCache
     * @return string
     */
    public static function snake($value, $delimiter = '_', $isCache = true)
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $isCache ? static::$snakeCache[$key][$delimiter] = $value : $value;
    }

    /**
     * 清除驼峰转下划线缓存
     */
    public static function clearSnakeCache()
    {
        self::$snakeCache = [];
    }

    /**
     * 下划线转驼峰(首字母小写)
     *
     * @param string $value
     * @param bool $isCache
     * @return string
     */
    public static function camel($value, $isCache = true)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        $value = lcfirst(static::studly($value));
        return $isCache ? static::$camelCache[$value] = $value : $value;
    }

    /**
     * 清除下划线转驼峰(首字母小写)缓存
     */
    public static function clearCamelCache()
    {
        self::$snakeCache = [];
    }

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @param string $value
     * @param bool $isCache
     * @return string
     */
    public static function studly($value, $isCache = true)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        $value = str_replace(' ', '', $value);

        return $isCache ? static::$studlyCache[$key] = $value : $value;
    }

    /**
     * 清除下划线转驼峰(首字母大写)缓存
     */
    public static function clearStudlyCache()
    {
        self::$snakeCache = [];
    }

    /**
     * 转为首字母大写的标题格式
     *
     * @param string $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * 实现多种字符编码方式
     *
     * @param string $input 数据源
     * @param string $_output_charset 输出的字符编码
     * @param string $_input_charset 输入的字符编码
     * @return string
     */
    public static function charsetEncode($input, $_output_charset, $_input_charset)
    {
        if (!isset ($_output_charset))
            $_output_charset = $_input_charset;
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            throw new \RuntimeException("不支持 $_input_charset 到 $_output_charset 编码！");
        }
        return $output;
    }

    /**
     * 实现多种字符解码方式
     *
     * @param string $input 数据源
     * @param string $_input_charset 输入的字符编码
     * @param string $_output_charset 输出的字符编码
     * @return string
     */
    public static function charsetDecode($input, $_input_charset, $_output_charset)
    {
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            throw new \RuntimeException("不支持 $_input_charset 到 $_output_charset 解码！");
        }
        return $output;
    }

    /**
     * 获取随机字符串
     *
     * @param int $length
     * @param int $type
     * @return string
     */
    public static function random($length = 16, $type = 5)
    {
        $pool = [
            0 => '0123456789',
            1 => 'abcdefghijklmnopqrstuvwxyz',
            2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ];

        $poolStr = '';
        if (0 == $type) $poolStr = $pool[0];
        if (1 == $type) $poolStr = $pool[1];
        if (2 == $type) $poolStr = $pool[2];
        if (3 == $type) $poolStr = $pool[0] . $pool[1];
        if (4 == $type) $poolStr = $pool[1] . $pool[2];
        if (5 == $type) $poolStr = $pool[0] . $pool[1] . $pool[2];

        return self::substr(str_shuffle(str_repeat($poolStr, $length)), 0, $length);
    }

    /**
     * 截取字符串
     *
     * @param string $string
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * 生成随机字符串
     *
     * @param string $factor
     * @return string
     */
    public static function nonceHash32($factor = '')
    {
        return md5(uniqid(md5(microtime(true) . $factor), true));
    }

    /**
     * 创建订单编号
     *
     * @param string $prefix
     * @return string
     */
    public static function makeOrderSn($prefix = '')
    { // 取出订单编号
        $datetime  = date('YmdHis');
        $microtime = explode(' ', microtime());
        $microtime = intval($microtime[0] ? $microtime[0] * 100000 : 100000);
        $nonceStr  = substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $prefix . $datetime . $microtime . $nonceStr;
    }

    /**
     * 解析Url Query
     *
     * @param string $url url地址或URL query参数
     * @return array
     */
    public static function parseUrlQuery($url)
    {
        $index = strpos($url, "?");
        $url   = $index === false ? $url : substr($url, $index);
        parse_str($url, $result);
        return $result;
        //
        //		//分隔数据
        //		$querys = explode("&", $url);
        //		unset($url);
        //		$regx = "/\\[(.*?)\\]/";
        //		//返回的结果
        //		$result = [];
        //		foreach($querys as $key => $val){
        //			list($name, $value) = explode('=', $val, 2);
        //			unset($querys[$key]);
        //
        //			$name = trim($name);
        //			//是否为数组
        //			if(preg_match($regx, $name, $matches)){
        //				$matches = $matches[1];
        //				$name = substr($name, 0, strpos($name, "[") - 1);
        //				if(empty($matches))
        //					$result[$name][] = $value;
        //				else
        //					$result[$name][$matches] = $value;
        //			}else{
        //				$result[$name] = $value;
        //			}
        //		}
        //		return $result;
    }

    /**
     * 把数组所有元素按照“参数=参数值”的模式用“&”字符拼接成字符串
     *
     * @param array $params 关联数组
     * @param string $handleFunc 值处理函数
     * @return string
     */
    public static function buildUrlQuery($params, $handleFunc = null)
    {
        if (!is_callable($handleFunc)) $handleFunc = function ($key, $val) {
            $type = gettype($val);
            if ($type == 'object' || $type == 'array') return '';

            $val = urlencode($val);
            return $key . '=' . $val;
        };

        $result = '';
        $i      = 0;
        foreach ($params as $key => $val) {
            $str = $handleFunc($key, $val);
            if ($str === '') continue;
            $result .= ($i === 0 ? '' : '&') . $str;
            $i++;
        }
        return $result;
    }

    /**
     * 将XML转换成数组
     *
     * @param string $xml
     * @return mixed
     */
    public static function parseXml($xml)
    {
        //将XML转为array,禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $info = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $info;
    }

    /**
     * 数组转XML(微信)
     *
     * @param array $param 要转换的数组
     * @param string $root 根元素
     * @param string $tag 指定元素标签名称，主要用于索引数组
     * @return string
     */
    public static function encodeXml($param, $root = 'xml', $tag = '')
    {
        if (!is_array($param) || count($param) <= 0) return '';

        $xml = '';
        foreach ($param as $key => $val) {
            $key = empty($tag) ? $key : $tag;
            if (is_int($val)) {
                $xml  .= "<" . $key . ">" . $val . "</" . $key . ">";
                $root = !empty($tag) ? '' : $root;
            } elseif (is_array($val)) {
                $tempRoot = Arr::isAssoc($param) ? $key : '';
                $tempTag  = (Arr::isAssoc($param) && !Arr::isAssoc($val)) ? $key : '';
                $xml      .= self::encodeXml($val, $tempRoot, $tempTag);
            } else {
                $xml  .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
                $root = !empty($tag) ? '' : $root;
            }
        }
        $xml = (empty($root) ? "" : "<$root>") . $xml . (empty($root) ? "" : "</$root>");
        return $xml;
    }

    /**
     * 解析UBB语法
     *
     * @param string $Text 要解析的文本
     * @return mixed|string
     */
    public static function parseUBB($Text)
    {
        $Text = trim($Text);
        $Text = htmlspecialchars($Text);
        $Text = preg_replace("/\\t/is", "  ", $Text);
        $Text = preg_replace("/\\[h1\\](.+?)\\[\\/h1\\]/is", "<h1>\\1</h1>", $Text);
        $Text = preg_replace("/\\[h2\\](.+?)\\[\\/h2\\]/is", "<h2>\\1</h2>", $Text);
        $Text = preg_replace("/\\[h3\\](.+?)\\[\\/h3\\]/is", "<h3>\\1</h3>", $Text);
        $Text = preg_replace("/\\[h4\\](.+?)\\[\\/h4\\]/is", "<h4>\\1</h4>", $Text);
        $Text = preg_replace("/\\[h5\\](.+?)\\[\\/h5\\]/is", "<h5>\\1</h5>", $Text);
        $Text = preg_replace("/\\[h6\\](.+?)\\[\\/h6\\]/is", "<h6>\\1</h6>", $Text);
        $Text = preg_replace("/\\[separator\\]/is", "", $Text);
        $Text = preg_replace("/\\[center\\](.+?)\\[\\/center\\]/is", "<span style=\"text-align: center\">\\1</span>",
            $Text);
        $Text = preg_replace("/\\[url=http:\\/\\/([^\\[]*)\\](.+?)\\[\\/url\\]/is", "<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $Text);
        $Text = preg_replace("/\\[url=([^\\[]*)\\](.+?)\\[\\/url\\]/is", "<a href=\"http://\\1\" target=\"_blank\">\\2</a>", $Text);
        $Text = preg_replace("/\\[url\\]http:\\/\\/([^\\[]*)\\[\\/url\\]/is", "<a href=\"http://\\1\" target=\"_blank\">\\1</a>", $Text);
        $Text = preg_replace("/\\[url\\]([^\\[]*)\\[\\/url\\]/is", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $Text);
        $Text = preg_replace("/\\[img\\](.+?)\\[\\/img\\]/is", "<img src=\"\\1\">", $Text);
        $Text = preg_replace("/\\[color=(.+?)\\](.+?)\\[\\/color\\]/is", "<span style=\"color: \\1\">\\2</span>", $Text);
        $Text = preg_replace("/\\[size=(.+?)\\](.+?)\\[\\/size\\]/is", "<span style=\"font-size: \\1\">\\2</span>", $Text);
        $Text = preg_replace("/\\[sup\\](.+?)\\[\\/sup\\]/is", "<sup>\\1</sup>", $Text);
        $Text = preg_replace("/\\[sub\\](.+?)\\[\\/sub\\]/is", "<sub>\\1</sub>", $Text);
        $Text = preg_replace("/\\[pre\\](.+?)\\[\\/pre\\]/is", "<pre>\\1</pre>", $Text);
        $Text = preg_replace("/\\[email\\](.+?)\\[\\/email\\]/is", "<a href=\"mailto:\\1\">\\1</a>", $Text);
        $Text = preg_replace("/\\[colorTxt\\](.+?)\\[\\/colorTxt\\]/eis", "color_txt('\\1')", $Text);
        $Text = preg_replace("/\\[emot\\](.+?)\\[\\/emot\\]/eis", "emot('\\1')", $Text);
        $Text = preg_replace("/\\[i\\](.+?)\\[\\/i\\]/is", "<i>\\1</i>", $Text);
        $Text = preg_replace("/\\[u\\](.+?)\\[\\/u\\]/is", "<u>\\1</u>", $Text);
        $Text = preg_replace("/\\[b\\](.+?)\\[\\/b\\]/is", "<b>\\1</b>", $Text);
        $Text = preg_replace("/\\[quote\\](.+?)\\[\\/quote\\]/is", " <div class=\"quote\"><h5>引用:</h5><blockquote>\\1</blockquote></div>", $Text);
        $Text = preg_replace("/\\[code\\](.+?)\\[\\/code\\]/eis", "highlight_code('\\1')", $Text);
        $Text = preg_replace("/\\[php\\](.+?)\\[\\/php\\]/eis", "highlight_code('\\1')", $Text);
        $Text = preg_replace("/\\[sig\\](.+?)\\[\\/sig\\]/is", "<div class=\"sign\">\\1</div>", $Text);
        $Text = preg_replace("/\\n/is", "<br/>", $Text);
        return $Text;
    }


    /**
     * 判断字符串是否为utf8编码，英文和半角字符返回ture
     * @param $string
     * @return bool
     */
    public static function is_utf8($string)
    {
        return preg_match('%^(?:
                    [\x09\x0A\x0D\x20-\x7E] # ASCII
                    | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
                    | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
                    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
                    | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
                    | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
                    | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
                    | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
                    )*$%xs', $string);
    }
    
    /**
	 * 安全处理-字符串或数组转数组
	 * @param mixed         $value
	 * @param string        $format
	 * @param string        $delimiter
	 * @param bool|\Closure $filter
	 * @return array
	 */
    public static function explode($value, $format = 'intval', $delimiter = ',', $filter = true){
		if(!is_array($value)){
			$value = is_string($value) ? explode($delimiter, $value) : [$value];
		}

		//		foreach($value as $k => &$v){
		//			if('intval' == $format){
		//				$v = intval($v);
		//			}elseif('floatval' == $format){
		//				$v = floatval($v);
		//			}elseif('boolval' == $format){
		//				$v = boolval($v);
		//			}elseif('long2ip' == $format){
		//				$v = long2ip($v);
		//			}
		//		}
		//		unset($v);

		$value = array_map($format, $value);

		if($filter !== false){
			if($filter === true){
				$value = array_filter($value);
			}else{
				$value = array_filter($value, $filter);
			}
		}

		return array_values($value);
	}

	/**
	 * 安全处理-数组转字符串
	 * @param mixed  $value
	 * @param string $format
	 * @param string $delimiter
	 * @return string
	 */
	public static function implode($value, $format = 'intval', $delimiter = ','){
		//先转换为数组，进行安全过滤
		$value = self::explode($value, $format, $delimiter);

		//去除重复
		$value = array_unique($value);

		//再次转换为字符串
		return implode(",", $value);
	}


    /**
     * 对用户的密码进行加密
     * @param $password
     * @param $encrypt //传入加密串，在修改密码时做认证
     * @return array/password
     */
    public static function md5pw($password, $encrypt = '')
    {
        $pwd             = [];
        $pwd['encrypt']  = $encrypt ? $encrypt : self::create_randomstr();
        $pwd['password'] = md5(md5(trim($password)) . $pwd['encrypt']);
        return $encrypt ? $pwd['password'] : $pwd;
    }


    /**
     * 产生随机字符串
     *
     * @param int $length 输出长度
     * @param string $chars 可选的 ，默认为 0123456789
     * @return   string     字符串
     */
    /*    public static function random($length, $chars = '0123456789') {
            $hash = '';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
            return $hash;
        }*/


    /**
     * 生成随机字符串
     * @param string $lenth 长度
     * @return string 字符串
     */

    public static function create_randomstr($lenth = 6)
    {
        return self::random($lenth, 3);
    }
 /**
     * 产生多个随机汉字
     * 可以创建新用户
     * @param int $num为生成汉字的数量
     * @return   string     字符串
     */
    public static function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
        // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }

    /**
    * @param $ary 要md5的数组
    * @return   string     字符串
    */
    public static function arrayMD5($ary, $isKsort = true)
    {
        if ($isKsort) {
            ksort($ary);
        } else {
            krsort($ary);
        }
        $befStr = '';
        foreach ( $ary as $k => $v ) {
            if(!empty($v)){
                $befStr .= "{$k}={$v}&";
            }
        }
        $befStr = rtrim($befStr, '&');
        return MD5($befStr);
    }

      /**
     * Generate name based md5 gen_union_id (version 3).
     * @example '7e57d0042b970e7ab45f5387367791cd'
     * @example '6991bfa4b0834538861d3fd6a40aaef0'
     */
    public static function gen_union_id()
    {
        // fix for compatibility with 32bit architecture; seed range restricted to 62bit
        $seed = mt_rand(0, 2147483647) . '#' . mt_rand(0, 2147483647);

        // Hash the seed and convert to a byte array
        $val  = md5($seed, true);
        $byte = array_values(unpack('C16', $val));

        // extract fields from byte array
        $tLo  = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
        $tMi  = ($byte[4] << 8) | $byte[5];
        $tHi  = ($byte[6] << 8) | $byte[7];
        $csLo = $byte[9];
        $csHi = $byte[8] & 0x3f | (1 << 7);

        // correct byte order for big edian architecture
        if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
            $tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8)
                | (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
            $tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
            $tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
        }

        // apply version number
        $tHi &= 0x0fff;
        $tHi |= (3 << 12);

        // cast to string
        $uuid = sprintf(
            '%08x%04x%04x%02x%02x%02x%02x%02x%02x%02x%02x',
            $tLo,
            $tMi,
            $tHi,
            $csHi,
            $csLo,
            $byte[10],
            $byte[11],
            $byte[12],
            $byte[13],
            $byte[14],
            $byte[15]
        );

        return strtoupper($uuid);
    }

    public static function uuid()
    {
        // fix for compatibility with 32bit architecture; seed range restricted to 62bit
        $seed = mt_rand(0, 2147483647) . '#' . mt_rand(0, 2147483647);

        // Hash the seed and convert to a byte array
        $val  = md5($seed, true);
        $byte = array_values(unpack('C16', $val));

        // extract fields from byte array
        $tLo  = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
        $tMi  = ($byte[4] << 8) | $byte[5];
        $tHi  = ($byte[6] << 8) | $byte[7];
        $csLo = $byte[9];
        $csHi = $byte[8] & 0x3f | (1 << 7);

        // correct byte order for big edian architecture
        if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
            $tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8)
                | (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
            $tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
            $tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
        }

        // apply version number
        $tHi &= 0x0fff;
        $tHi |= (3 << 12);

        // cast to string
        $uuid = sprintf(
            '%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            $tLo,
            $tMi,
            $tHi,
            $csHi,
            $csLo,
            $byte[10],
            $byte[11],
            $byte[12],
            $byte[13],
            $byte[14],
            $byte[15]
        );

        return $uuid;
    }

    /**
     * 判断手机号码格式是否正确
     * @param $email
     */
    public static function is_mobilephone($mobilephone)
    {
        return
            strlen($mobilephone) > 9 && preg_match("/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/", $mobilephone);
    }

    /**
     * 手机号码格式转换【18455667788 转为184-5566-7788】
     * @param type $mobile
     */
    public static function mobile2string($mobilephone)
    {
        if (!self::is_mobilephone($mobilephone)) {
            return false;
        }
        $char1           = substr($mobilephone, 0, 3);
        $char2           = substr($mobilephone, 4, 4);
        $char3           = substr($mobilephone, -4);
        $new_mobilephone = $char1 . ' ' . $char2 . ' ' . $char3;
        return $new_mobilephone;
    }


    /**
     * 手机号码中间四位以‘****’代替
     * @param type $mobile
     * return  string
     */
    public static function mobile_enctry($mobile) {
        if (!self::is_mobilephone($mobile)) {
            return false;
        }
        $mobile = substr_replace($mobile, '****', 4, 4);
        return $mobile;
    }

    public static function user_name_enctry($mobile) {
        $mobile = self::truncate_utf8_string($mobile, 3, "****");
        return $mobile;
    }

    /**
     * 汉字转拼音
     * @param $string 汉字字符串
     * @param $type 1返回首字母 2返回所有
     * @param $is_all 是否转换全拼 head转换首字母 all转换全拼
     * @return $string
     */
    public static function zh2pinyin($string, $type = 1, $is_all = 'head')
    {
        if (empty($string)) {
            return '';
        }
        $zh2py_class = base::load_sys_class("CUtf8_PY");
        $str         = $zh2py_class->encode($string, $is_all);
        if ($type == 1) {
            $str = substr($str, 0, 1);
        } else {
            $str = $str;
        }
        return ucwords($str);
    }

    
    /**
     * 查询字符是否存在于某字符串
     *
     * @param $haystack 字符串
     * @param $needle 要查找的字符
     * @return bool
     */
    public static function str_exists($haystack, $needle) {
        return !(strpos($haystack, $needle) === FALSE);
    }

    /**
     * 省份名称转换
     *
     * @param string $province 当$province值为空时，直接返回省份数组信息
     * @param int $type 1：'北京市'=>'北京'，2：'北京'=>'北京市'
     * @return string or array
     */
    public static function replace_province($province, $type = 1) {
        $tmparr = array('北京市' => '北京', '安徽省' => '安徽', '福建省' => '福建', '甘肃省' => '甘肃', '广东省' => '广东', '广西壮族自治区' => '广西', '贵州省' => '贵州', '海南省' => '海南', '河北省' => '河北', '河南省' => '河南', '黑龙江省' => '黑龙江', '湖北省' => '湖北', '湖南省' => '湖南', '吉林省' => '吉林', '江苏省' => '江苏', '江西省' => '江西', '辽宁省' => '辽宁', '内蒙古自治区' => '内蒙古', '宁夏回族自治区' => '宁夏', '青海省' => '青海', '山东省' => '山东', '山西省' => '山西', '陕西省' => '陕西', '上海' => '上海', '四川省' => '四川', '天津' => '天津', '西藏自治区' => '西藏', '新疆维吾尔自治区' => '新疆', '云南省' => '云南', '浙江省' => '浙江', '重庆' => '重庆', '香港特别行政区' => '香港', '澳门特别行政区' => '澳门', '台湾省' => '台湾', '其它' => '其他');
        if ($province) {
            if ($type == 2) {
                $tmparr = array_flip($tmparr);
            }
            return $tmparr[$province] ? $tmparr[$province] : $province;
        }
        return $tmparr;
    }
    /**
     * 截断utf8字符串
     *
     * @param string $string 
     * @param int $length ：3 
     * @param string $etc ：'...'
     * @return string
     */
    public static function truncate_utf8_string($string, $length, $etc = '...') {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }


    /**
     * 金额转为繁体字（最高到千万元）
     * @param int $num ：300  
     * @return string 叁佰元
    */
    public static function price2zh($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2); 
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        } 
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            } 
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            } 
            $j = $j + 3;
        } 
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        return $c;
    }

}
