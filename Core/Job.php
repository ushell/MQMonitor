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

        if (! function_exists('swoole_timer_tick'))
        {
            throw new Exception("swoole_timer_tick function not found, reinstall swoole php extension!");
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
