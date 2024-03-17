<?php
declare (strict_types = 1);

namespace mztp\ext;

use think\facade\Log;

/**
 * 扩展日志调用
 *
 * @Author Mirze
 */
class MLog 
{
    // 校验log.php中channel名
    private static function getChannelName($channel='file')
    {
        $channels = config("log.channels");
        return isset($channels[$channel]) ? $channel : 'file';
    }
    
    /**
     * Undocumented function
     *
     * @param string $channel
     * @param string $level
     * @param string $data
     * @return void
     * @Author Mirze
     * @DateTime 2021-05-19
     */
    static function write($channel='file', $level='info', $data='')
    {
        if(is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        $channel = SELF::getChannelName($channel);
        Log::channel($channel)->$level($data);
    }
    
    /**
     * HTTP日志：
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     * @DateTime 2021-07-28
     */
    static function http($data='', $level='info')
    {
        SELF::write('http', $level, $data);
    }
    
    /**
     * Debug调试测试日志
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     * @DateTime 2021-07-28
     */
    static function debug($data='', $level='info')
    {
        SELF::write('debug', $level, $data);
    }

    /**
     * 操作日志：
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     * @DateTime 2021-05-19
     */
    static function opt($data='', $level='info')
    {
        SELF::write('opt', $level, $data);
    }

    /**
     * 操作日志：
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     * @DateTime 2021-05-19
     */
    static function db($data='', $level='info')
    {
        SELF::write('db', $level, $data);
    }
    
    /**
     * 关键日志：敏感信息操作
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     */
    static function crux($data='', $level='info')
    {
        SELF::write('crux', $level, $data);
    }

    /**
     * 关键日志：登录日志提取
     *
     * @param string $data 数据：可数组
     * @param string $level 日志通道：apart_level
     * @return void
     * @Author Mirze
     * @DateTime 2024-03-17
     */
    static function login($data='', $level='info')
    {
        SELF::write('login', $level, $data);
    }
    
}