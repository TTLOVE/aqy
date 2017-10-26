<?php

/**
 * 这是一个驱动, 介绍了如何使用 leaf-loger, 向文件中写日志
 * 你可以将当前这个 LogDriver比作是你的框架，如此集成其道你的项目中
 * 另外, 该驱动实现了基本你可能需要的方法, 如日志记录 info, error, 以及获取文件处理器日志记录位置等
 * 本驱动主要做了3个事情
 * 1. 实例化loger, 该loger可做成单例模式, 是否单例取决于你, 本driver中, 并未处理成单例
 * 2. 实例化logHandlerManager, 并交给loger示例, 当然, 倘若你没有实例化这个 logHandlerManage 也可以, 因为loger内部已经做了这个事情
 * 3. 设置日志处理器, 比如文件处理器, 可设置多个, 设置完成后添加到 loger 中
 */

namespace Leaf\Loger;

use \Psr\Log;
use \Leaf\Loger\Handler\HandlerFile;
use \Leaf\Loger\Loger;

class LogDriver
{

    private static $instance = null;
    private $loger = null;
    private $logConfig = null;

    public function __construct()
    {
        $this->logConfig = require BASE_PATH.'/Config/log.php';
        self::init();
    }

    private function init()
    {
        /**
         * 实例化loger
         */
        $this->loger = new Loger();;
        /**
         * 设置日志处理器之文件处理器
         */
        $fileHandler = new HandlerFile();
        /**
         * 将文件日志处理器添加到loger中
         */
        $this->getLoger()->addHandler('file', $fileHandler);
    }

    /**
     * 获取日志记录器
     *
     * @return Loger
     */
    public function getLoger()
    {
        return $this->loger;
    }

    public function emergency($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->emergency($message, $context, $category);
    }

    public function error($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->error($message, $context, $category);
    }

    public function warning($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->warning($message, $context, $category);
    }

    public function alert($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->alert($message, $context, $category);
    }

    public function critical($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->critical($message, $context, $category);
    }

    public function notice($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->notice($message, $context, $category);
    }

    public function info($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->info($message, $context, $category);
    }

    public function debug($logKey, $message, array $context = [], $category = '')
    {
        if ( isset($this->logConfig[ $logKey ]) ) {
            $this->setLogFile($this->logConfig[ $logKey ] . date("Ymd") . ".log");
        }
        $this->getLoger()->debug($message, $context, $category);
    }

    /**
     * 获取文件处理器日志记录路径
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->getLoger()->getLogHandlerManager()->getSomeLogHandler('file')->getLogFile();
    }

    /**
     * 设置文件处理器日志记录路径
     *
     * @param string $file
     *
     * @return  void
     */
    public function setLogFile($file)
    {
        $this->getLoger()->getLogHandlerManager()->getSomeLogHandler('file')->setLogFile($file);
    }

}
