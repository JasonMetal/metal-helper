<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

final class Other
{
    /**
     *获取客户端ip地址
     * @return 返回IP地址
     */
    public static function get_client_ip()
    {
        $realip = null;
        //判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }
        if ($realip && strpos($realip, ',')) {
            $realip = explode(',', $realip)[0];
        }
        return $realip;
    }

    /**
 * @Notes  : 获取客户端server ip 地址
 * @return : 返回server ip地址 array|false|mixed|string
 */
    public static function getServerIp() {
        if (isset($_SERVER)) {
            if($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } else {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

    /**
     * 获取客户端IP地址[已集成 CDN获取底层用户IP]
     * @return 返回IP地址
     */
    public static function i2c_realip()
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
    public static function getIp()
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
     * 返回经stripslashes处理过的字符串或数组
     * @param $string 需要处理的字符串或数组
     * @return mixed
     */
    public static function new_stripslashes($string)
    {
        if (!is_array($string))
            return stripslashes($string);
        foreach ($string as $key => $val)
            $string[$key] = new_stripslashes($val);
        return $string;
    }

    /**
     * 返回经htmlspecialchars处理过的字符串或数组
     * @param $obj 需要处理的字符串或数组
     * @return mixed
     */
    public static function new_html_special_chars($string)
    {
        $encoding = 'utf-8';
        if (strtolower(CHARSET) == 'gbk')
            $encoding = 'gb2312';
        if (!is_array($string))
            return htmlspecialchars($string, ENT_QUOTES, $encoding);
        foreach ($string as $key => $val)
            $string[$key] = new_html_special_chars($val);
        return $string;
    }

    public static function new_html_entity_decode($string)
    {
        $encoding = 'utf-8';
        if (strtolower(CHARSET) == 'gbk')
            $encoding = 'gb2312';
        return html_entity_decode($string, ENT_QUOTES, $encoding);
    }

    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    public static function safe_replace($string)
    {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }

    /**
     * xss过滤函数
     *
     * @param $string
     * @return string
     */
    public static function remove_xss($string)
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
        $parm1  = ['javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'];
        $parm2  = [
            'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload',
        ];
        $parm   = array_merge($parm1, $parm2);
        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string  = preg_replace($pattern, '', $string);
        }
        return $string;
    }

    /**
     * 过滤ASCII码从0-28的控制字符
     * @return String
     */
    public static function trim_unsafe_control_chars($str)
    {
        $rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
        return str_replace(chr(0), '', preg_replace($rule, '', $str));
    }

    /**
     * 格式化文本域内容
     *
     * @param $string 文本域内容
     * @return string
     */
    public static function trim_textarea($string)
    {
        $string = nl2br(str_replace(' ', '&nbsp;', $string));
        return $string;
    }

    /**
     * 将文本格式成适合js输出的字符串
     * @param string $string 需要处理的字符串
     * @param intval $isjs 是否执行字符串格式化，默认为执行
     * @return string 处理后的字符串
     */
    public static function format_js($string, $isjs = 1)
    {
        $string = addslashes(str_replace(["\r", "\n", "\t"], ['', '', ''], $string));
        return $isjs ? 'document.write("' . $string . '");' : $string;
    }

    /**
     * 转义 javascript 代码标记
     *
     * @param $str
     * @return mixed
     */
    public static function trim_script($str)
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = trim_script($val);
            }
        } else {
            $str = preg_replace('/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str);
            $str = preg_replace('/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str);
            $str = preg_replace('/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str);
            $str = str_replace('javascript:', 'javascript：', $str);
        }
        return $str;
    }

    /**
     * 获取当前页面完整URL地址
     */
    public static function get_url()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self     = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
        $path_info    = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url   = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }

    /**
     * 过滤a标签中某些特定域名外的链接 
     * 
     * @param string $content 
     * @return string 
     */
    public static function strip_html_a($content) {
        $pre = "/<a (.*?)>(.*?)<\/a>/i";
        preg_match_all($pre, $content, $tmparr);
        if ($tmparr) {
            foreach ($tmparr[0] as $key => $val) {
                preg_match_all("/href\s*=\s*(\"|\')?https?:\/\/([^\.]+\.)*" . substr(ROOTDOMAIN, 1) . "/i", $val, $tmpval);
                if ($tmpval[0][0]) {
                    continue;
                }
                $content = str_replace($tmparr[0][$key], $tmparr[2][$key], $content);
            }
        }
        return $content;
    }

    /**
     * 创建(导出)Excel数据表格
     * @param array $list 要导出的数组格式的数据
     * @param string $filename 导出的Excel表格数据表的文件名
     * @param array $indexKey $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
     * @param array $startRow 第一条数据在Excel表格中起始行
     * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表
     *      $filename = '配置数据'.date('Y-m-d_H点i分');
            $field = [
                'name'=>'名称',
                'menu_name'=>'目录',//...
                ];
            $indexkey = array_keys($field);
            $list = array_merge([$field],$list);
            export_excel($list,$filename,$indexkey);
     * 比如: $indexKey与$list数组对应关系如下:
     *     $indexKey = array('id','username','sex','age');
     *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24));
     */
    public static function export_excel($list, $filename, $indexKey, $startRow = 1, $excel2007 = false) {
        require(Env::get('root_path') . '/vendor/phpexcel/PHPExcel.php');
        //文件引入
        if (empty($filename)) $filename = time();
        if (!is_array($indexKey)) return false;

        $header_arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        //初始化PHPExcel()
        $objPHPExcel = new PHPExcel();

        //设置保存版本格式
        if ($excel2007) {
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $filename = $filename . '.xlsx';
        } else {
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $filename = $filename . '.xls';
        }

        //接下来就是写数据到表格里面去
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$startRow = 1;
        foreach ($list as $row) {
            foreach ($indexKey as $key => $value) {
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key] . $startRow, $row[$value]);
            }
            $startRow++;
        }

        // 下载这个表格，在浏览器输出
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename=' . $filename . '');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

}
  

