<?php
/**
 * 
 *
 * @copyright (c) 2015~2020 Metal All rights reserved.
 * @license       http://www.apache.org/licenses/LICENSE-2.0
 * 
 */

namespace  metal\helper;

/**
 * 目录操作类
 *
 * @package  metal\helper
 */
final class File{

	/**
	 * 获取指定目录下所有的文件，包括子目录下的文件
	 *
	 * @param string $dir
	 * @return array
	 */
	public static function getFiles($dir){
		$files = [];
		$each = function($dir) use (&$each, &$files){
			$it = new \FilesystemIterator($dir);
			/**@var $file \SplFileInfo */
			foreach($it as $file){
				if($file->isDir()){
					$each($file->getPathname());
				}else{
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
	 * @param string   $dir
	 * @param callable $callback
	 */
	public static function each($dir, callable $callback){
		$each = function($dir) use (&$each, $callback){
			$it = new \FilesystemIterator($dir);
			/**@var $file \SplFileInfo */
			foreach($it as $file){
				if($callback($file) === false){
					return false;
				}

				if($file->isDir()){
					if($each($file->getPathname()) === false){
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
	public static function delete($dir){
		$each = function($dir) use (&$each){
			if(!is_dir($dir)) return true;
			$it = new \FilesystemIterator($dir);
			$flag = true;
			/**@var $file \SplFileInfo */
			foreach($it as $file){
				if($file->isDir()){
					if($each($file->getPathname()) === true){
						if(!@rmdir($file->getPathname()))
							$flag = false;
					}else{
						$flag = false;
					}
				}else{
					if(!@unlink($file->getPathname()))
						$flag = false;
				}
			}
			return $flag;
		};

		if($each($dir) === true){
			if(!is_dir($dir) || @rmdir($dir)){
				return true;
			}
		}

		return false;
	}

	/**
	 * 基于数组创建目录和文件
	 *
	 * @param array $files
	 */
	public static function createDirOrFiles(array $files){
		foreach($files as $key => $value){
			$deep = substr($value, -1);
			if($deep == DIRECTORY_SEPARATOR){
				mkdir($value);
			}else{
				@file_put_contents($value, '');
			}
		}
	}
}
