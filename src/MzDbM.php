<?php
declare (strict_types = 1);

namespace mztp;

use think\facade\Db;

use mztp\facade\FCurdMDb;

/**
 * 继承业务逻辑基类：跨库访问支持
 *
 * @Author Mirze
 */
class MzDbM 
{
    // 表是否存在
    function tableExist($tableName='', $dbName='')
    {
        if(empty($tableName)) return false;
        // $isExist = Db::query("show tables like '$tableName' ");

        $isExist = Db::connect($dbName)->query("show tables like '$tableName' ");
        return $isExist ? true : false;
    }

    // 更改表状态
    function changeState($id=0, $state=0, $uid=0, $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);

        $data['state'] = $state;
        $data['opt_uid'] = $uid;
        $data['update_time'] = time();
        return FCurdMDb::update($id, $data);
    }

    // 伪删除
    function del($id=0, $optUid=0, $tableName='', $dbName='')
    {
        return $this->changeState($id, -1, $optUid);
    }

    // 查询单条记录:id
    function readOne($id=0, $field="", $isOutField=false, $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);
        return FCurdMDb::getOne($id, $field, $isOutField);
    }

    // 查询单条记录:map
    function readRow($map=[], $field='', $isOutField=false, $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);
        return FCurdMDb::getRow($map, $field, $isOutField);
    }

    // 聚合查询：count,max,min,avg,sum
    function gnn($map=[], $func='count', $field='id', $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);
        return FCurdMDb::gnn($map, $func, $field);
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
    function saveUpdate($oper=0, $data=[], $map=[], $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);

        $time = time();
        $data['update_time'] = $time;
        if($oper == 1) {
            $data['create_time'] = $time;
            return FCurdMDb::insert($data);
        } elseif($oper == 2) {
            return FCurdMDb::update($map, $data);
        } else {}
        return 0;
    }

    // 页面列表分页
    function listPage($isArr=false, $map=[], $order='', $field="", $isOutField=false, $pageSize=20, $pageNum=1, $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);
        return FCurdMDb::listPage($isArr, $map, $order, $field, $isOutField, $pageSize, $pageNum);
    }

    // 获取所有数据数组
    function listAll($map=[], $order='', $field="", $isOutField=false, $tableName='', $dbName='')
    {
        FCurdMDb::init($tableName, $dbName);
        return FCurdMDb::listAll($map, $order, $field, $isOutField);
    }
    
}