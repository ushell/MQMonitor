<?php
namespace Monitor\Core;

use Exception;

class Job
{
    /**
     * 定时器
     * @param  [callable] $callback [description]
     * @param  [int] $mtime    [description]
     * @return [int]           [timer id]
     */
    public static function timer($callback, $mtime)
    {
        if (! is_callable($callback))
        {
            throw new Exception("Error callable object !");
        }

        return swoole_timer_tick($mtime, $callback);
    }

    /**
     * 清除定时器
     * @param  [int] $timeId [description]
     * @return [bool]         [description]
     */
    public static function clearTimer($timeId)
    {
        return swoole_timer_clear($timeId);
    }

}
