<?php

namespace Leaf\Loger\LogerClass;

use Leaf\Loger\Handler\Handler;

/**
 * Class LogerManager
 *
 * @package Leaf\Loger\LogerClas
 */
class LogHandlerManager
{

    /**
     * instances of log handler objects such as fileHandler
     *
     * @var array
     */
    protected $handlers = [];

    public function __construct()
    {
    }

    /**
     * add a log handler
     *
     * @param string     $handlerName
     * @param LogHandler $handler
     */
    public function addHandler($handlerName, Handler $handler)
    {
        if (empty( $handlerName ) || empty( $handler )) {
            throw new \InvalidArgumentException('handlerName or handler can\'t be empty');
        }
        $this->handlers[$handlerName] = $handler;
    }

    /**
     * remove a log handler
     *
     * @param string $handlerName
     *
     * @return bool
     */
    public function removeHandler($handlerName)
    {
        $removed = false;
        if (empty( $handlerName )) {
            throw new \InvalidArgumentException('handlerName can\'t be empty');
        }
        if (isset( $this->handlers[$handlerName] )) {
            unset( $this->handlers[$handlerName] );
            $removed = true;
        }

        return $removed;
    }

    /**
     * handle log info
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function handle($level, $message, array $context = [], $category = '')
    {
        foreach ($this->handlers as $handlerObj) {
            $handlerObj->handle($level, $message, $context, $category);
        }
    }

    /**
     * get a log handler with it's name, for example: you can a file handler with its handler name such as 'file'
     *
     * @param string $logHandlerName handlerName like : file, sms, mail etc
     */
    public function getSomeLogHandler($logHandlerName = '')
    {
        if ( !empty( $logHandlerName )) {
            return isset( $this->handlers[$logHandlerName] ) ? $this->handlers[$logHandlerName] : null;
        }
        else {
            throw new \InvalidArgumentException('empty logHandlerName');
        }
    }

    /**
     * set the log level type of current log handler
     *
     * @param string $level it can only be one of types of self::$logType
     *
     * @return $this
     */
    public function setLogLevel($level = LogLevel::NOTICE)
    {
        foreach ($this->handlers as $handlerObj) {
            /**
             * $handlerObj
             * @var Handler
             */
            $handlerObj->setLogLevel($level);
        }

        return $this;
    }

    /**
     * set the log category types of current log handler
     *
     * @param string it can be something like application,debug
     *
     * @return $this
     */
    public function setLogCategory($category = '')
    {
        foreach ($this->handlers as $handlerObj) {
            /**
             * $handlerObj
             * @var Handler
             */
            $handlerObj->setLogCategory($category);
        }

        return $this;
    }

}