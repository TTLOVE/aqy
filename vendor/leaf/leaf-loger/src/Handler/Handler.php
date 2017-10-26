<?php

namespace Leaf\Loger\Handler;

use Leaf\Loger\LogerClass\LogLevel;

/**
 * Class HandlerBase
 *
 * @package Leaf\Loger\Handler
 */
abstract class Handler
{

    protected $logMessage = [];

    protected $logFormat = [
        'message'   => '',      //message (mixed, can be a string or some complex data, such as an exception object)
        'level'     => '',      //level (string)
        'timestamp' => '',      //timestamp (float, obtained by microtime(true))
        'category'  => '',      //category (string)
        'trace'     => '',      //traces (array, debug backtrace, contains the application code call stacks)
    ];

    /**
     * all log levels
     *
     * @var array
     */
    protected $logType = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    /**
     * the current log level
     * if you record a log message with a lower level, the handler will ignore it. so, you can only record logs with a
     * higher level type than self::$logLevel
     *
     * @var string
     */
    protected $logLevel = LogLevel::DEBUG;

    /**
     * the log category of current log handler
     * when setted, this log handler can record logs with avalid categorys
     *
     * @var array
     */
    protected $logCategory = [];

    /**
     * get formated time, Will output something like: 2014-01-01 12:20:24.423421
     *
     * @param string $format
     * @param int    $utimestamp
     *
     * @return bool|string
     */
    public function getLogTime($format = 'Y-m-d H:i:s', $utimestamp = 0)
    {
        return date($format);
    }

    /**
     * checkout if the level input is alloawable
     *
     * @param string $level
     *
     * @return bool
     */
    protected function checkLogLevel($level)
    {
        $logTypeLevelNum = array_flip($this->logType);
        $return = in_array($level,
            $this->logType) && ( $logTypeLevelNum[$level] <= $logTypeLevelNum[$this->logLevel] ) ? true : false;

        return $return;
    }

    /**
     * check if the log category input is alloawable
     *
     * @param $category
     *
     * @return bool
     */
    protected function checkLogCategory($category)
    {
        $return = true;
        if ( !empty( $this->logCategory ) && !in_array($category, $this->logCategory)) {
            $return = false;
        }

        return $return;
    }

    /**
     * handle a log info
     *
     * @param string $level    log level, it can only be one of self::$logType
     * @param string $message  log message
     * @param array  $context  log context
     * @param string $category log category if empty, the handler will use 'application' as default value
     */
    public function handle($level, $message, array $context = [], $category = '')
    {
        if (empty( $level ) || empty( $message )) {
            throw new \InvalidArgumentException('param error: level or message can\'t be empty');
        }
        $category = !empty( $category ) ? $category : 'application';
        if ( !$this->checkLogLevel($level) || !$this->checkLogCategory($category)) {
            return;
        }
        $logInfo = [
            'level'     => $level,
            'message'   => $message,
            'timestamp' => $this->getLogTime(),
            'category'  => $category,
        ];
        $logInfo = array_merge($logInfo, $context);
        $this->logMessage[] = $logInfo;
        $this->afterHanle();
    }

    protected function afterHanle()
    {

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
        if (in_array($level, $this->logType)) {
            $this->logLevel = $level;
        }
        else {
            throw new \InvalidArgumentException('wrong level type!');
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
        if ( !empty( $category )) {
            $arrCategory = explode(',', $category);
            $this->logCategory = $arrCategory;
        }
        else {
            throw new \InvalidArgumentException('wrong level type!');
        }

        return $this;
    }

}