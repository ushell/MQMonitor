<?php
namespace Monitor\Core;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;

class Command extends SymfonyCommand
{
    /**
     * 命令名称
     * @var string
     */
	public $name = '命令';

    /**
     * 命令说明
     * @var string
     */
    public $description = '描述';

    /**
     * 帮助信息
     * @var string
     */
    public $help = '帮助';

    /**
     * 参数
     * @var string
     */
    public $args = [
        'username' => InputArgument::REQUIRED,
    ];

    /**
     * 命令配置
     * @return [type] [description]
     */
    public function configure()
    {
        $this->setName($this->name)->setDescription($this->description)->setHelp($this->help);

        //设置参数
        foreach ($this->args as $name => $mode) {
            $this->addArgument($name, $mode);
        }
    }

    /**
     * 输出带颜色
     * 
     * @param  OutputInterface $output [description]
     * @return [type]                  [description]
     */
    public function outputWithColor(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('red', 'black', array('bold', 'blink'));
        $output->getFormatter()->setStyle('hint', $style);

        return $output;
    }

}

