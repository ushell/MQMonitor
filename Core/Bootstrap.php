<?php
namespace Monitor\Core;

class Bootstrap
{
    /**
     * 基础路径
     * @var [type]
     */
    public static $basePath;

    /**
     * 模块
     * @var array
     */
    public static $modules = [];

    /**
     * 自动加载模块
     * @param  [type] $basePath [description]
     * @return [type]           [description]
     */
    public static function autoload($basePath)
    {
        self::$basePath = $basePath;

        self::findModule();

        return self::$modules;
    }

    /**
     * 查找模块
     * @return [type] [description]
     */
    public static function findModule()
    {
        $path = self::$basePath . '/module';

        $fd = opendir($path);
        if (! $fd)
        {
            throw new Exception("Module directory %s error", $path);
        }

        while(($name = readdir($fd)) !== FALSE)
        {
            if (in_array($name, ['.', '..']))
            {
                continue;
            }

            $bootstrap = sprintf("%s/module/%s/bootstrap.php", self::$basePath, $name);
            if (file_exists($bootstrap))
            {
                self::$modules[] = require($bootstrap);
            }
        }
    }

    public function teardownModule()
    {

    }

    public function bindModule()
    {

    }

}