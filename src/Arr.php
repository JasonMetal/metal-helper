<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @copyright (c) 2015~2019 BD All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author BD<657306123@qq.com>
 */
namespace  metal\helper;

/**
 * 数组工具类
 *
 * @package  metal\helper
 */
final class Arr{

	/**
	 * 是否为关联数组
	 *
	 * @param array $arr 数组
	 * @return bool
	 */
	public static function isAssoc($arr){
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * 不区分大小写的in_array实现
	 *
	 * @param $value
	 * @param $array
	 * @return bool
	 */
	public static function in($value, $array){
		return in_array(strtolower($value), array_map('strtolower', $array));
	}

	/**
	 * 对数组排序
	 *
	 * @param array $param 排序前的数组
	 * @return array
	 */
	public static function sort(&$param){
		ksort($param);
		reset($param);
		return $param;
	}

	/**
	 * 除去数组中的空值和和附加键名
	 *
	 * @param array $params 要去除的数组
	 * @param array $filter 要额外过滤的数据
	 * @return array
	 */
	public static function filter(&$params, $filter = ["sign", "sign_type"]){
		foreach($params as $key => $val){
			if($val == "" || (is_array($val) && count($val) == 0)){
				unset ($params [$key]);
			}else{
				$len = count($filter);
				for($i = 0; $i < $len; $i++){
					if($key == $filter [$i]){
						unset ($params [$key]);
						array_splice($filter, $i, 1);
						break;
					}
				}
			}
		}
		return $params;
	}

	/**
	 * 数组栏目获取
	 *
	 * @param array  $array
	 * @param string $column
	 * @param string $index_key
	 * @return array
	 */
	public static function column(array $array, $column, $index_key = null){
		$result = [];
		foreach($array as $row){
			$key = $value = null;
			$keySet = $valueSet = false;
			if($index_key !== null && array_key_exists($index_key, $row)){
				$keySet = true;
				$key = (string)$row[$index_key];
			}
			if($column === null){
				$valueSet = true;
				$value = $row;
			}elseif(is_array($row) && array_key_exists($column, $row)){
				$valueSet = true;
				$value = $row[$column];
			}
			if($valueSet){
				if($keySet){
					$result[$key] = $value;
				}else{
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * 解包数组
	 *
	 * @param array        $array
	 * @param string|array $keys
	 * @return array
	 */
	public static function uncombine(array $array, $keys = null){
		$result = [];

		if($keys){
			$keys = is_array($keys) ? $keys : explode(',', $keys);
		}else{
			$keys = array_keys(current($array));
		}

		foreach($keys as $index => $key) $result[$index] = [];
		foreach($array as $item){
			foreach($keys as $index => $key){
				$result[$index][] = isset($item[$key]) ? $item[$key] : null;
			}
		}

		return $result;
	}

	/**
	 * 无极限分类
	 *
	 * @param array    $list 数据源
	 * @param callable $callback 额外处理回调函数
	 * @param int      $pid 父id
	 * @param string   $idName 检索对比的键名
	 * @param string   $parent 检索归属的键名
	 * @param string   $child 存放在哪？
	 * @return array
	 */
	public static function tree(array $list, callable $callback = null, $pid = 0, $idName = 'id', $parent = 'pid', $child = 'child'){
		$level = 0;
		$handler = function(array &$list, callable $callback, $pid, $idName, $parent, $child) use (&$handler, &$level){
			$level++;
			$array = [];
			foreach($list as $key => $value){
				if($value [$parent] == $pid){
					unset ($list [$key]);
					$callback($level, $value);

					$childList = $handler($list, $callback, $value [$idName], $idName, $parent, $child);
					if(!empty($childList)) $value [$child] = $childList;

					$array [] = $value;
					reset($list);
				}
			}
			$level--;
			return $array;
		};

		is_null($callback) && $callback = function(){ };
		return $handler($list, $callback, $pid, $idName, $parent, $child);
	}

	/**
	 * 树转tree
	 *
	 * @param array  $list
	 * @param string $child
	 * @return array
	 */
	public static function treeToList($list, $child = 'child'){
		$handler = function($list, $child) use (&$handler){
			$result = [];
			foreach($list as $key => &$val){
				$result[] = &$val;
				unset($list[$key]);
				if(isset($val[$child])){
					$result = array_merge($result, $handler($val[$child], $child));
					unset($val[$child]);
				}
			}
			unset($val);
			return $result;
		};
		return $handler($list, $child);
	}
}
