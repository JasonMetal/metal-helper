<?php
/**
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace metal\helper;

/**
 * 目录操作类
 *
 * @package  metal\helper
 */
final class File {

    /**
     * 获取指定目录下所有的文件，包括子目录下的文件
     *
     * @param string $dir
     * @return array
     */
    public static function getFiles($dir) {
        $files = [];
        $each  = function ($dir) use (&$each, &$files) {
            $it = new \FilesystemIterator($dir);
            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($file->isDir()) {
                    $each($file->getPathname());
                } else {
                    $files[] = $file;
                }
            }
        };
        $each($dir);
        return $files;
    }

    /**
     * 递归指定目录下所有的文件，包括子目录下的文件
     *
     * @param string $dir
     * @param callable $callback
     */
    public static function each($dir, callable $callback) {
        $each = function ($dir) use (&$each, $callback) {
            $it = new \FilesystemIterator($dir);
            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($callback($file) === false) {
                    return false;
                }

                if ($file->isDir()) {
                    if ($each($file->getPathname()) === false) {
                        return false;
                    }
                }
            }
            return true;
        };

        $each($dir);
    }

    /**
     * 删除文件或目录
     *
     * @param string $dir
     * @return bool
     */
    public static function delete($dir) {
        $each = function ($dir) use (&$each) {
            if (!is_dir($dir)) return true;
            $it   = new \FilesystemIterator($dir);
            $flag = true;
            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($file->isDir()) {
                    if ($each($file->getPathname()) === true) {
                        if (!@rmdir($file->getPathname()))
                            $flag = false;
                    } else {
                        $flag = false;
                    }
                } else {
                    if (!@unlink($file->getPathname()))
                        $flag = false;
                }
            }
            return $flag;
        };

        if ($each($dir) === true) {
            if (!is_dir($dir) || @rmdir($dir)) {
                return true;
            }
        }

        return false;
    }


    /**
     * 清空/删除 文件或文件夹下面所有文件
     * 路径为文件夹时,删除文件夹下所有内容,路径为指定文件时,只删除文件
     * @param string $dirname 路径
     * @param bool $self 是否删除自身
     * @return bool
     */
    public static function do_rmdir($dirname, $self = false) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            chmod($dirname, 0777);  //修改权限
            return @unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                do_rmdir($dirname . '/' . $entry);
            }
        }
        $dir->close();
        $self && rmdir($dirname);
    }


    /**
     * 基于数组创建目录和文件
     *
     * @param array $files
     */
    public static function createDirOrFiles(array $files) {
        foreach ($files as $key => $value) {
            $deep = substr($value, -1);
            if ($deep == DIRECTORY_SEPARATOR) {
                mkdir($value);
            } else {
                @file_put_contents($value, '');
            }
        }
    }

    /**
     * 取得文件扩展
     *
     * @param $filename 文件名
     * @return 扩展名
     */
    public static function fileext($filename) {
        return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
    }


    /**
     * 文件下载
     * @param $filepath 文件路径
     * @param $filename 文件名称
     */

    public static function is_ie() {
        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
        if (strpos($useragent, 'msie ') !== false) return true;
        return false;
    }

    public static function file_down($filepath, $filename = '') {
        if (!$filename)
            $filename = basename($filepath);
        if (self::is_ie())
            $filename = rawurlencode($filename);
        $filetype = self::fileext($filename);
        $filesize = sprintf("%u", filesize($filepath));
        if (ob_get_length() !== false)
            @ob_end_clean();
        header('Pragma: public');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Encoding: none');
        header('Content-type: ' . $filetype);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-length: ' . $filesize);
        readfile($filepath);
        exit;
    }

    /**
     * @Notes  : 扫描文件夹获取目录
     * ->@Notes  : 获取 xx
     * @param  :
     * @return :array
     * @user   : user
     * @time   : 2022/3/9_14:54
     */
    public static function scandirs($dir = './Uploads/') {
        $tmp  = [];
        $data = scandir($dir);
        foreach ($data as $value) {
            if ('.' != $value && '..' != $value) {
                $tmp[] = $value;
            }
        }
        return $tmp;
    }

    public static function scanfiles($path = './Uploads/ipcai/', $dir = '') {
        //打开当前文件夹由 $path 指定
        $handler = opendir($path);
        $jt      = [];
        while (($filename = readdir($handler)) !== false) {//略过linux目录的名字为'.'和‘..'的文件
            if ($filename != '.' && $filename != '..' && $filename != 'Thumbs.db') {//输出文件名
                $jt[] = $filename;
            }
        }
        closedir($handler);
        return $jt;
    }

}
