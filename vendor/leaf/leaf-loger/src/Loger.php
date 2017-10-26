<?php

/**
 * loger
 * you are recommend to handle loger,logHandlerManager and logHandler with dependent object such as leaf-di to loose
 * coupling.
 */


namespace Leaf\Loger;

use Leaf\Loger\Handler\Handler;
use Leaf\Loger\LogerClass\LogHandlerManager;
use Leaf\Loger\LogerClass\LogLevel;

//use Psr\Log\AbstractLogger;
//class Loger extends AbstractLogger

class Loger
{

    protected $logHandlerManager = null;

    public function __construct()
    {
        $this->init();
    }

    /**
     * init
     */
    public function init()
    {
        $logHandlerManager = new LogHandlerManager();
        $this->setLogHandlerManager($logHandlerManager);
    }

    /**
     * set logHandlerManager to this loger
     *
     * @param LogHandlerManager $logHandlerManager
     */
    public function setLogHandlerManager($logHandlerManager)
    {
        $this->logHandlerManager = $logHandlerManager;
    }

    /**
     * get the logHandlerManager
     *
     * @return LogHandlerManager
     */
    public function getLogHandlerManager()
    {
        return $this->logHandlerManager;
    }

    /**
     * get a log handler with it's name
     *
     * @param string $logHandlerName
     */
    public function getSomeLogHandler($logHandlerName)
    {
        if (empty( $logHandlerName ) && !is_null($this->logHandlerManager)) {
            return $this->getLogHandlerManager()->getSomeLogHandler($logHandlerName);
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function emergency($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::EMERGENCY, $message, $context, $category);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function alert($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::ALERT, $message, $context, $category);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function critical(string $message, array $context = [], $category = '')
    {
        $this->log(LogLevel::CRITICAL, $message, $context, $category);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function error($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::ERROR, $message, $context, $category);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function warning($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::WARNING, $message, $context, $category);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function notice($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::NOTICE, $message, $context, $category);
    }

    /**
     * important log
     *
     * sush as：login in log and SQL log。
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function info($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::INFO, $message, $context, $category);
    }

    /**
     * debug log
     *
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function debug($message, array $context = [], $category = '')
    {
        $this->log(LogLevel::DEBUG, $message, $context, $category);
    }

    /**
     * log
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @param string $category
     *
     * @return null
     */
    public function log($level, $message, array $context = [], $category = '')
    {
        $message .= '  --------from---------  ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $message .= '  --------para---------  ' . json_encode($_REQUEST);
        if (is_object(static::getLogHandlerManager()) && ( static::getLogHandlerManager() instanceof LogHandlerManager )) {
            static::getLogHandlerManager()->handle($level, $message, $context, $category);
        }
        else {
            throw new \UnexpectedValueException('logManager needed!');
        }
    }

    /**
     * add a log handler
     *
     * @param string  $handlerName handlerName
     * @param Handler $handler
     */
    public function addHandler($handlerName, Handler $handler)
    {
        static::getLogHandlerManager()->addHandler($handlerName, $handler);
    }

    /**
     * set the log level type of all the  log handler
     *
     * @param string $level it can only be one of types of self::$logType
     *
     * @return $this
     */
    public function setLogLevel($level = LogLevel::NOTICE)
    {
        if (is_object(static::getLogHandlerManager()) && ( static::getLogHandlerManager() instanceof LogHandlerManager )) {
            static::getLogHandlerManager()->setLogLevel($level);
        }
        else {
            throw new \UnexpectedValueException('logManager needed!');
        }

        return $this;
    }


    /**
     * set the log category types of all the log handler
     *
     * @param string $category it can be something like 'application,debug' or a single 'profile'
     *
     * @return $this
     */
    public function setLogCategory($category = '')
    {
        if (is_object(static::getLogHandlerManager()) && ( static::getLogHandlerManager() instanceof LogHandlerManager )) {
            static::getLogHandlerManager()->setLogCategory($category);
        }
        else {
            throw new \InvalidArgumentException('wrong level type!');
        }

        return $this;
    }


}
