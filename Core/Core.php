<?php
namespace Monitor\Core;

use Exception;
use Symfony\Component\Console\Application;

class Core extends Application
{
    /**
     * 应用
     */
    const APP = 'monitor';
    
    /**
     * 版本
     */
    const VERSION = '1.0.0';

    /**
     * 基础路径
     * @var [type]
     */
    public static $basePath;

    /**
     * 实例
     * @var [type]
     */
    protected $instance;

    /**
     * 绑定模列表
     * @var array
     */
    protected $bindings = [];

    /**
     * 绑定命令列表
     * @var array
     */
    protected $commands = [];

    /**
     * 配置
     * @var [type]
     */
    public $config = [];

    public function __construct($config)
    {
        $this->setBasePath();
        $this->parseConfig($config);
        $this->bootstrap();

        parent::__construct(self::APP, self::VERSION); 
    }

    /**
     * 设置基础路径
     */
    private function setBasePath()
    {
        self::$basePath = dirname(__DIR__);        
    }

    /**
     * 加载模块
     * @return [type] [description]
     */
    private function bootstrap()
    {
        $bootstrap = Bootstrap::autoload(self::$basePath);

        $this->singleton($bootstrap);

        foreach ($this->commands as $command)
        {
            parent::add($command);
        }
    }

    /**
     * 解析配置
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    private function parseConfig($config = [])
    {
        $this->config = $config;
    }

    /**
     * 绑定命令
     * @param  [type] $name   [description]
     * @param  [type] $module [description]
     * @return [type]         [description]
     */
    public function bind($name, $module)
    {
        if (! isset($module['command']))
        {
            throw new Exception("Error Module [%s]", json_encode($name));
        }

        $instance = new $module['command'];

        array_push($this->commands, $instance);
    }
    
    public function make($bootstrap = [])
    {
        foreach ($bootstrap as $module)
        {
            foreach ($module as $name => $command)
            {
                if (! isset($this->bindings[$name]))
                {
                    $this->bindings[$name] = $this->bind($name, $command);
                }
            }
        }
    }

    /**
     * 单例
     * @param  [type] $bootstrap [description]
     * @return [type]            [description]
     */
    public function singleton($bootstrap)
    {
        $this->make($bootstrap);
    }

}
