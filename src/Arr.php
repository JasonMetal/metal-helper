<?php
/**

 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace  metal\helper;

/**
 * 数组工具类
 *
 * @package  metal\helper
 */
final class Arr{


    protected static function p($data, $mark = '')
    {
        // 定义样式
        $str = '<pre style="display: block;width:2000px;padding: 9.5px;margin: 14px 0 0 0;font-size: 15px;line-height: 1.42857;color: #0b7500;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data = $data ? 'true' : 'false';
        } elseif (is_null($data)) {
            $show_data = 'null';
        } else {
            $show_data = print_r($data, true);
        }
        $str .= $mark . " => ";
        $str .= $show_data;
        $str .= '</pre>';
        echo $str;
    }

	protected static function dd($result,$mark = '')
    {
        p($result,$mark);
        exit();
        echo '</pre>';
	}
	

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

		foreach($keys as $index => $key){
			$result[$index] = [];
		}

		foreach($array as $item){
			foreach($keys as $index => $key){
				$result[$index][] = isset($item[$key]) ? $item[$key] : null;
			}
		}

		return $result;
	}

	/**
	 * 数组去重-二维数组
	 * @param array  $array
	 * @param string $key
	 * @return array
	 */
	public static function multiUnique($array, $key){
		$i = 0;
		$temp_array = [];
		$key_array = [];

		foreach($array as $val){
			if(!in_array($val[$key], $key_array)){
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
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

	/**
	 * 转换数组里面的key
	 *
	 * @param array $arr
	 * @param array $keyMaps
	 * @return array
	 */
	public static function transformKeys(array $arr, array $keyMaps){
		foreach($keyMaps as $oldKey => $newKey){
			if(!array_key_exists($oldKey, $arr)) continue;

			if(is_callable($newKey)){
				list($newKey, $value) = call_user_func($newKey, $arr[$oldKey], $oldKey, $arr);
				$arr[$newKey] = $value;
			}else{
				$arr[$newKey] = $arr[$oldKey];
			}
			unset($arr[$oldKey]);
		}
		return $arr;
	}

    /**
     * 获得所有父级栏目
     *
     * @param array  $data 栏目数据
     * @param int    $sid 子栏目
     * @param string $primaryId 唯一键名，如果是表则是表的主键
     * @param string $parentId 父ID键名
     * @return array
     */
    public static function getParentChannel(array $data, int $sid, $primaryId = 'id', $parentId = 'pid')
    {
        if (empty($data)) {
            return $data;
        }

        $arr = [];
        foreach ($data as $v) {
            if ($v[$primaryId] == $sid) {
                $arr[] = $v;
                $_n    = self::getParentChannel($data, $v[$primaryId], $primaryId, $parentId);
                if (!empty($_n)) {
                    $arr = array_merge($arr, $_n);
                }
            }
        }

        return $arr;
    }


    /**
     * 获取除指定键数组外的所有给定数组。
     *
     * @param array $array 原数组
     * @param array $keys 要舍去的key
     *
     * @return array
     * @author King
     */
    public static function except(array $array, array $keys)
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * 二分查找
     *
     * @param int|float $number 查找数
     * @param array     $array 待查找区间
     *
     * @return int
     * @author King
     */
    public static function binarySearch($number, array $array)
    {
        if (!is_array($array) || empty($array)) {
            return -1;
        }

        sort($array);

        $length = count($array);
        $lower  = 0;
        $high   = $length - 1;

        while ($lower < $high) {
            $middle = intval(($lower + $high) / 2);
            if ($array[$middle] > $number) {
                $high = $middle - 1;
            } elseif ($array[$middle] < $number) {
                $lower = $middle + 1;
            } else {
                return $middle;
            }
        }

        return -1;
    }

}
