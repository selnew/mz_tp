# mz_tp
thinkphp6 composer library

## 目录结构
~~~
src
    curd    基于thinkphp Db 基类CURD封装
    ext     扩展类：方法
    
~~~

## 使用
```
// Log
use mztp\ext\MLog;
$error['exception'] = "测试composer MLog";
MLog::db($error, 'error');

// CURD
use mztp\curd\CurdDb;
$db = new CurdDb();
$row = $db->getOne('tabname',1);

```