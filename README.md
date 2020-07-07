# metal-helper
PHP项目日常开发必备基础库，数组工具类、字符串工具类、数字工具类、函数工具类、服务器工具类、加密工具类
github2packagist

```PHP
/**
 * 获取客户的IP地址
 *
 * @return string
 */
/**
 * @Notes  : xx 模块
 * ->@Notes  : 获取 xx
 * @return :mixed|string
 * @user   : XiaoMing
 * @time   : 2020/7/7_11:06
 */
function getRemoteIp()
{
    if (isset($_SERVER ["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER ["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER ["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER ["REMOTE_ADDR"])) {
        $ip = $_SERVER ["REMOTE_ADDR"];
    } else {
        $ip = "0.0.0.0";
    }

    return $ip;
}

/**
 * 获取客户端端口号
 *
 * @return int
 */
function getRemotePort()
{
    $port = 0;
    if (isset($_SERVER ["REMOTE_PORT"])) {
        $port = $_SERVER ["REMOTE_PORT"];
    } elseif (isset($_COOKIE ["REMOTE_PORT"])) {
        $port = $_COOKIE ["REMOTE_PORT"];
    } elseif (isset($_POST ["REMOTE_PORT"])) {
        $port = $_POST ["REMOTE_PORT"];
    } elseif (isset($_GET ["REMOTE_PORT"])) {
        $port = $_GET ["REMOTE_PORT"];
    }

    return $port;
}

/**
 * 获取主机名称
 *
 * @return string
 */
function getServerName()
{
    return $_SERVER ['SERVER_NAME'];
}

/**
 * 获取当前访问的文件
 *
 * @return string
 */
function getExecuteFile()
{
    $urls = explode('/', strip_tags($_SERVER ['REQUEST_URI']), 2);
    return count($urls) > 1 ? $urls [1] : '';
}

/**
 * 获取所有请求头信息
 *
 * @return array
 */
function getAllHeader()
{
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if ('HTTP_' == substr($key, 0, 5)) {
            $headers [str_replace('_', '-', substr($key, 5))] = $value;
        }
    }
    if (isset ($_SERVER ['PHP_AUTH_DIGEST'])) {
        $headers ['AUTHORIZATION'] = $_SERVER ['PHP_AUTH_DIGEST'];
    } elseif (isset ($_SERVER ['PHP_AUTH_USER']) && isset ($_SERVER ['PHP_AUTH_PW'])) {
        $headers ['AUTHORIZATION'] = base64_encode($_SERVER ['PHP_AUTH_USER'] . ':' . $_SERVER ['PHP_AUTH_PW']);
    }
    if (isset ($_SERVER ['CONTENT_LENGTH'])) {
        $headers ['CONTENT-LENGTH'] = $_SERVER ['CONTENT_LENGTH'];
    }
    if (isset ($_SERVER ['CONTENT_TYPE'])) {
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
function getClientName($isVersion = true)
{
    // 获取客户端版本信息
    $getVersion = function ($str, $checkname) {
        $pos = strpos($str, $checkname);
        $len = strpos($str, ';', $pos);
        $len = $len ? $len - $pos : strlen($str) - $pos;
        return substr($str, $pos, $len);
    };

    $info = getClientInfo();
    if (strpos($info ['info_str'], 'windows phone') !== false) {
        if (!$isVersion) return "windows phone";
        return $getVersion($info ['info_str'], 'windows phone');
    } else {
        if (strpos($info ['info_str'], 'windows') !== false) {
            if (!$isVersion) return "windows";
            return $getVersion($info ['info_str'], 'windows');
        } elseif (strpos($info ['info_str'], 'android') !== false) {
            if (!$isVersion) return "android";
            return $getVersion($info ['info_str'], 'android');
        } elseif (strpos($info ['info_str'], 'iphone') !== false) {
            if (!$isVersion) return "iphone";
            return $getVersion($info ['info_str'], 'iphone');
        } elseif (strpos($info ['info_str'], 'mac os') !== false) {
            if (!$isVersion) return "mac os";
            return $getVersion($info ['info_str'], 'mac os');
        } elseif (is_array($info ['info'])) {
            if ($isVersion == false) {
                $info ['info'] = explode(" ", $info ['info'] [0]);
            }
            return $info ['info'] [0];
        } else {
            return "other";
        }
    }
}

/**
 * 获取终端信息
 *
 * @return array
 */
function getClientInfo()
{
    $info                = [];
    $user_agent          = strtolower($_SERVER ['HTTP_USER_AGENT']);
    $firstSpilt          = strpos($user_agent, ')');
    $user_agent2         = substr($user_agent, 0, $firstSpilt);
    $user_agents         = explode(" ", $user_agent2, 2);
    $mozilla             = explode("/", $user_agents [0], 2);
    $info [$mozilla [0]] = $mozilla [1];
    $user_agent2         = substr($user_agents [1], 1);
    $info ['info']       = explode("; ", $user_agent2);
    $info ['info_str']   = $user_agent2;

    // applewebkit/537.36
    $user_agent2 = substr($user_agent, $firstSpilt + 2, strlen($user_agent) - $firstSpilt);
    $user_agent2 = preg_replace('/(\(.*\))\s/', "", $user_agent2);
    $user_agents = explode(" ", $user_agent2);
    $len         = count($user_agents);
    for ($i = 0; $i < $len; $i++) {
        $temps             = explode("/", $user_agents [$i], 2);
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
function serializeParams($isExportStyle = true)
{
    if ($isExportStyle) {
        return var_export([
            "GET"     => $_GET,
            "POST"    => $_POST,
            "COOKIE"  => $_COOKIE,
            "SESSION" => $_SESSION,
            "SERVER"  => $_SERVER,
        ], true);
    }
    return "[GET=" . http_build_query($_GET) . "],"
        . "[POST=" . http_build_query($_POST, false) . "]," .
        "[COOKIE=" . http_build_query($_COOKIE, false) . "]," .
        "[SESSION=" . http_build_query($_SESSION, false) . "]," .
        "[SERVER=" . http_build_query($_SERVER, false) . "]";
}

/**
 * 是否移动端访问访问
 *
 * @return bool
 */
function isMobileVisit()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
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
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
                (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
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
function flushScriptCall($callback, $param = '')
{
    if (is_string($param)) {
        $param = '"' . addslashes($param) . '"';
    } else {
        if (is_array($param)) {
            if (key($param) == 0) {
                $tmpStr = '';
                $len    = count($param);
                for ($i = 0; $i < $len; $i++) {
                    if ($i != 0) $tmpStr .= ",";
                    $tmpStr .= '"' . addslashes($param) . '"';
                }
                $param = $tmpStr;
            } else {
                $param = json_encode($param);
            }
        }
    }
    flushScript("{$callback} ( " . json_encode($param) . " )");
}

/**
 * 发送到客户端script
 *
 * @param string $script
 */
function flushScript($script)
{
    echo "<script type=\"text/javascript\">{$script}</script>";
    flush();
    ob_flush();
}

/**
 * URL重定向
 *
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '')
{
    //多行URL地址支持
    $url = str_replace(["\n", "\r"], '', $url);
    if (empty($msg)) {
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    }

    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv=\"Refresh\" content=\"{$time};URL={$url}\">";
        if (0 != $time) {
            $str .= $msg;
        }
        exit($str);
    }
}

/**
 *获取客户端ip地址
 * @return 返回IP地址
 */
function get_client_ip()

{
    if ($_SERVER['REMOTE_ADDR']) {
        $cip = $_SERVER['REMOTE_ADDR'];
    } elseif (getenv("REMOTE_ADDR")) {
        $cip = getenv("REMOTE_ADDR");
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $cip = getenv("HTTP_CLIENT_IP");
    } else {
        $cip = "unknown";
    }
    return $cip;
}

/**
 * 获取客户端IP地址[已集成 CDN获取底层用户IP]
 * @return 返回IP地址
 */
function i2c_realip()
{
    $ip = FALSE;
    if ($_SERVER["HTTP_CDN_SRC_IP"]) {
        return $_SERVER["HTTP_CDN_SRC_IP"];
    }
    // If HTTP_CLIENT_IP is set, then give it priority
    if (!empty($_SERVER ["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER ["HTTP_CLIENT_IP"];
    }
    // User is behind a proxy and check that we discard RFC1918 IP addresses
    // if they are behind a proxy then only figure out which IP belongs to the
    // user.  Might not need any more hackin if there is a squid reverse proxy
    // infront of apache.
    if (!empty($_SERVER ['HTTP_X_FORWARDED_FOR'])) {

        // Put the IP's into an array which we shall work with shortly.
        $ips = explode(", ", $_SERVER ['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }

        for ($i = 0; $i < count($ips); $i++) {
            // Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and
            // 192.168.0.0/16
            if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./', $ips [$i])) {
                if (version_compare(phpversion(), "5.0.0", ">=")) {
                    if (ip2long($ips [$i]) != false) {
                        $ip = $ips [$i];
                        break;
                    }
                } else {
                    if (ip2long($ips [$i]) != -1) {
                        $ip = $ips [$i];
                        break;
                    }
                }
            }
        }
    }
    // Return with the found IP or the remote address
    return ($ip ? $ip : $_SERVER ['REMOTE_ADDR']);
}


// 获取客户端IP地址
//function get_client_ip(){
//    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
//        $ip = getenv("HTTP_CLIENT_IP");
//    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
//        $ip = getenv("HTTP_X_FORWARDED_FOR");
//    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
//        $ip = getenv("REMOTE_ADDR");
//    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
//        $ip = $_SERVER['REMOTE_ADDR'];
//    else
//        $ip = "unknown";
//    return($ip);
//}


function getIp()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"]) && strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown")) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]) && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown")) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            if (!empty($_SERVER["REMOTE_ADDR"]) && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
                $ip = $_SERVER["REMOTE_ADDR"];
            } else {
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],
                        "unknown")
                ) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ip = "unknown";
                }
            }
        }
    }
    return ($ip);
}


/**
 * Generate name based md5 gen_union_id (version 3).
 * @example '7e57d0042b970e7ab45f5387367791cd'
 * @example '6991bfa4b0834538861d3fd6a40aaef0'
 */
function gen_union_id()
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

function uuid()
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

#程序开始时间
/**
 * @start time
 */
function proStartTime()
{
    global $startTime;
    $mtime1    = explode(" ", microtime());
    $startTime = $mtime1[1] + $mtime1[0];
}

/**
 * @End time
 */
function proEndTime()
{
    global $startTime, $set;
    $mtime2    = explode(" ", microtime());
    $endtime   = $mtime2[1] + $mtime2[0];
    $totaltime = ($endtime - $startTime);
    $totaltime = number_format($totaltime, 7);
    //process time:
    return $totaltime;
}


//传递数据以易于阅读的样式格式化后输出
//by metal
if (!function_exists('p')) {

    function p($data)
    {
        $str = '---';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data = $data ? 'true' : 'false';
        } elseif (is_null($data)) {
            $show_data = 'null';
        } else {
            $show_data = print_r($data, true);
        }
        $str .= $show_data;
        $str .= '---';
        echo $str;
    }

    function pp($data)
    {
        // 定义样式
        $str = '<pre style="display: block;width:2000px;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #0b7500;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data = $data ? 'true' : 'false';
        } elseif (is_null($data)) {
            $show_data = 'null';
        } else {
            $show_data = print_r($data, true);
        }
        $str .= $show_data;
        $str .= '</pre>';
        echo $str;
    }


}



```
