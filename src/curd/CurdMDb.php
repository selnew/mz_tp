<?php
declare (strict_types = 1);

namespace mztp\curd;

use think\facade\Db;
use mztp\ext\MLog;

/**
 * 读取表数据: 动态连接数据库, 支持跨库
 * 注：操作前请初始化对象 FCurdMDb::init($dbName,$tableName);
 *
 * @Author Mirze
 */
class CurdMDb
{
    public $db = null;
    public $pageSize = 15;

    // 创建实例db读取表对象
    function init($tableName='', $dbName='')
    {
        if(empty($tableName)) return null;
        $obj = Db::connect($dbName)->table($tableName);
        $this->db = $obj;
        return $obj;
    }

    /**
     * 查询字段方式
     *
     * @param string $field 字段
     * @param boolean $isOutField 是否排除查询字段值：true 排除字段 false 直接字段
     * @return boolean
     */
    function isWithout($field="", $isOutField=false)
    {
        $obj = ($isOutField) ? $this->db->withoutField($field) : $this->db->field($field);
        $this->db = $obj;
        return $obj;
    }

    // 获取主键ID对应字段值
    function getFieldValue($id=0, $field='', $pk='id')
    {
        if($id < 1) return '';
        $val = '';
        try {
            $val = $this->db->where($pk, $id)->value($field);
        } catch (\Exception $e) {
        }
        return $val == null ? '' : $val;
    }

    // 按ID查询记录
    function getOne($id=0, $field="", $isOutField=false)
    {
        if($id < 1) return [];
        
        $row = [];
        try {
            $this->isWithout($field, $isOutField);
            $row = $this->db->find($id);
        } catch (\Exception $e) {
        }
        return $row;
    }

    // 按查询条件获取一条记录
    function getRow($map=[], $field="", $isOutField=false)
    {
        if(empty($map)) return [];
        $row = [];
        try {
            $this->isWithout($field, $isOutField);
            $row = $this->db->where($map)->findOrEmpty();
        } catch (\Exception $e) {
        }
        return $row;
    }
    
    // 
    function insert($data=[])
    {
        if(empty($data)) return 0;

        $id = 0;
        try {
            $id = $this->db->strict(false)->insertGetId($data);
        } catch (\Exception $e) {
            $error['req'] = $data;
            $error['exception'] = $e->getMessage();
            MLog::db($error, 'error');
        }
        return $id;
    }

    /**
     * 更新
     *
     * @param array/int $map 条件：int值为id更新
     * @param array $data
     * @return void
     */
    function update($map, $data=[])
    {
        if(empty($map) || empty($data)) return 0;

        $res = 0;
        try {
            $map = is_array($map) ? $map : ['id' => $map];
            $res = $this->db->strict(false)->where($map)->update($data);
        } catch (\Exception $e) {
            $error['req'] = $data;
            $error['map'] = $map;
            $error['exception'] = $e->getMessage();
            MLog::db($error, 'error');
        }
        return $res;
    }

    /**
     * 聚合查询：
     *
     * @param string $tableName 表名
     * @param array $map 查询条件
     * @param string $func 聚合函数
     * @param string $field 查询字段
     * @return void
     */
    function gnn($map=[], $func='count', $field='id')
    {
        $funcArr = ['count','max','min','avg','sum'];
        $func = strtolower($func);
        if(!in_array($func, $funcArr)) return 0;

        $num = 0;
        try {
            $num = $this->db->where($map)->$func($field);
        } catch (\Exception $e) {
        }
        return $num;
    }
    
    /**
     * 获取分页列表数据：
     *
     * @param boolean $isArr 是否数组分页：true 数组分页 false 列表分页
     * @param array $map 查询条件
     * @param string $order 排序
     * @param string $field 字段
     * @param boolean $isOutField 是否排除字段
     * @param integer $pageSize 每页记录条数
     * @param integer $pageNum 页码
     * @return void
     */
    function listPage($isArr=false, $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1)
    {
        $orderTmp = ['id'=>'desc'];
        $order = empty($order) ? $orderTmp : $order;

        $list = [];
        try {
            $this->isWithout($field, $isOutField);

            $obj = $this->db->where($map)->order($order);

            $pageSize = $pageSize > 0 ? $pageSize : $this->pageSize;
            $pageNum = $pageNum > 1 ? $pageNum : 1; // 页码
            if($isArr) {
                // 数组分页：page
                $list = $obj->page($pageNum, $pageSize)->select();
                $list = $list->toArray();
            } else {
                // 列表分页：paginate
                $pageParam['list_rows'] = $pageSize;
                $pageParam['query'] = request()->param();

                $list = $obj->paginate($pageParam);
            }
        } catch (\Exception $e) {
        }
        return $list;
    }

    /**
     * 获取所有数据数组
     *
     * @param array $map 查询条件
     * @param string $order 排序
     * @param string $field 字段
     * @param boolean $isOutField 是否排除字段
     * @return void
     */
    function listAll($map=[], $order='', $field="", $isOutField=false)
    {
        $orderTmp = ['id'=>'desc'];
        $order = empty($order) ? $orderTmp : $order;

        $list = [];
        try {
            $this->isWithout($field, $isOutField);

            $list = $this->db->where($map)->order($order)->select();
            $list = $list->toArray();
        } catch (\Exception $e) {
        }
        return $list;
    }

}