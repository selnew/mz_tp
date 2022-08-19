<?php
namespace mztp\facade;

use think\Facade;

/**
 * 门面(facade): Curd类
 *
 * @Author Mirze
 */
class FCurdDb extends Facade
{
    protected static function getFacadeClass()
    {
        return 'mztp\curd\CurdDb';
    }
}