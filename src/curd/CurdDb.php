<?php
declare (strict_types = 1);

namespace mztp\curd;

use think\facade\Db;
use mztp\ext\MLog;


/**
 * 直接读取表数据：单库
 *
 * @Author Mirze
 */
class CurdDb
{
    public $pageSize = 20;

    // 获取主键ID对应字段值
    function getFieldValue($tableName='', $id=0, $field='', $pk='id')
    {
        if($id < 1) return '';
        $val = Db::table($tableName)->where($pk, $id)->value($field);
        return $val == null ? '' : $val;
    }

    function getOne($tableName='', $id=0, $field="", $isOutField=false)
    {
        if($id < 1) return [];
        if($isOutField) {
            $row = Db::table($tableName)->withoutField($field)->find($id);
        } else {
            $row = Db::table($tableName)->field($field)->find($id);
        }
        return $row;
    }

    function getRow($tableName='', $map=[], $field="", $isOutField=false)
    {
        if(empty($map)) return [];
        if($isOutField) {
            $row = Db::table($tableName)->where($map)->withoutField($field)->findOrEmpty();
        } else {
            $row = Db::table($tableName)->where($map)->field($field)->findOrEmpty();
        }        
        return $row;
    }

    function insert($tableName='', $data=[])
    {
        $id = 0;
        try {
            $id = Db::table($tableName)->strict(false)->insertGetId($data);
        } catch (\Exception $e) {
            $error['req'] = $data;
            $error['exception'] = $e->getMessage();
            MLog::db($error, 'error');
        }
        return $id;
    }

    function update($tableName='', $map, $data=[])
    {
        if(empty($map) || empty($data)) return 0;

        $res = 0;
        try {
            $map = is_array($map) ? $map : ['id' => $map];
            $res = Db::table($tableName)->strict(false)->where($map)->update($data);
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
    function gnn($tableName='', $map=[], $func='count', $field='id')
    {
        $funcArr = ['count','max','min','avg','sum'];
        $func = strtolower($func);
        if(!in_array($func, $funcArr)) return 0;

        $num = 0;
        try {
            $num = Db::table($tableName)->where($map)->$func($field);
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
    function listPage($tableName='', $isArr=false, $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1)
    {
        $orderTmp = ['id'=>'desc'];
        $order = empty($order) ? $orderTmp : $order;

        $list = [];
        try {
            $obj = Db::table($tableName);
            if($isOutField) {
                $obj = $obj->withoutField($field);
            } else {
                $obj = $obj->field($field);
            }

            $obj = $obj->where($map)->order($order);

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
     * 数组分页：返回data、count、page、size
     *
     * @param string $tableName 表名
     * @param array $map 查询条件
     * @param string $order 排序
     * @param string $field 字段
     * @param boolean $isOutField 是否排除字段
     * @param integer $pageSize 每页记录条数
     * @param integer $pageNum 页码
     * @return void
     * @Author Mirze
     * @DateTime 2022-12-14
     */
    function arrPage($tableName='', $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1)
    {
        $orderTmp = ['id'=>'desc'];
        $order = empty($order) ? $orderTmp : $order;

        $list = [];
        $count = 0;
        try {
            $obj = Db::table($tableName);
            $count = $obj->where($map)->count();

            if($isOutField) {
                $obj = $obj->withoutField($field);
            } else {
                $obj = $obj->field($field);
            }
            $obj = $obj->where($map)->order($order);

            $pageSize = $pageSize > 0 ? $pageSize : $this->pageSize;
            $pageNum = $pageNum > 1 ? $pageNum : 1; // 页码

            // 数组分页：page
            $list = $obj->page($pageNum, $pageSize)->select();
            $list = $list->toArray();
        } catch (\Exception $e) {
        }

        $result['list'] = $list;
        $result['count'] = $count;
        $result['page'] = $pageNum;
        $result['size'] = $pageSize;
        return $result;
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
    function listAll($tableName='', $map=[], $order='', $field="", $isOutField=false)
    {
        $orderTmp = ['id'=>'desc'];
        $order = empty($order) ? $orderTmp : $order;

        $list = [];
        try {
            $obj = Db::table($tableName);
            if($isOutField) {
                $obj = $obj->withoutField($field);
            } else {
                $obj = $obj->field($field);
            }

            $list = $obj->where($map)->order($order)->select();
            $list = $list->toArray();
        } catch (\Exception $e) {
        }
        return $list;
    }

}