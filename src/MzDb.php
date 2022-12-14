<?php
declare (strict_types = 1);

namespace mztp;

use think\facade\Db;

use mztp\facade\FCurdDb;

/**
 * 继承业务逻辑基类：DB
 *
 * @Author Mirze
 */
class MzDb 
{
    // 表是否存在
    function tableExist($tableName='')
    {
        if(empty($tableName)) return false;
        $isExist = Db::query("show tables like '$tableName' ");
        return $isExist ? true : false;
    }

    // 更改表状态
    function changeState($tableName='', $id=0, $state=0, $uid=0)
    {
        $data['state'] = $state;
        $data['opt_uid'] = $uid;
        $data['update_time'] = time();
        return FCurdDb::update($tableName, $id, $data);
    }

    // 伪删除
    function del($tableName='',$id=0, $optUid=0)
    {
        return $this->changeState($tableName, $id, -1, $optUid);
    }

    // 获取主键ID对应字段值
    function readValue($tableName='', $id=0, $field='', $pk='id')
    {
        return FCurdDb::getFieldValue($tableName, $id, $field, $pk);
    }

    // 查询单条记录:id
    function readOne($tableName='', $id=0, $field='', $isOutField=false)
    {
        return FCurdDb::getOne($tableName, $id, $field, $isOutField);
    }

    // 查询单条记录:map
    function readRow($tableName='', $map=[], $field="", $isOutField=false)
    {
        return FCurdDb::getRow($tableName, $map, $field, $isOutField);
    }

    // 聚合查询：count,max,min,avg,sum
    function gnn($tableName='', $map=[], $func='count', $field='id')
    {
        return FCurdDb::gnn($tableName, $map, $func, $field);
    }
    
    /**
     * 保存/更新
     *
     * @param integer $oper 操作：1 添加 2 更新
     * @param array $data 数据
     * @param array/int $map 更新条件：int时为id值
     * @return void
     * @Author Mirze
     */
    function saveUpdate($tableName='', $oper=0, $data=[], $map=[])
    {
        $time = time();
        $data['update_time'] = $time;
        if($oper == 1) {
            $data['create_time'] = $time;
            return FCurdDb::insert($tableName, $data);
        } elseif($oper == 2) {
            return FCurdDb::update($tableName, $map, $data);
        } else {}
        return 0;
    }

    // 页面列表分页
    function listPage($tableName='', $isArr=false, $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1)
    {
        return FCurdDb::listPage($tableName, $isArr, $map, $order, $field, $isOutField, $pageSize, $pageNum);
    }

    // 数组分页：返回data、count、page、size
    function arrPage($tableName='', $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1)
    {
        return FCurdDb::listPage($tableName, $map, $order, $field, $isOutField, $pageSize, $pageNum);
    }

    // 获取所有数据数组
    function listAll($tableName='',$map=[], $order='', $field="", $isOutField=false)
    {
        return FCurdDb::listAll($tableName, $map, $order, $field, $isOutField);
    }
    
}