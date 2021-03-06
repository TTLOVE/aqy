<?php

namespace Leaf\Loger\Handler;

/**
 * file handler
 * Class HandlerFile
 *
 * @package Leaf\Loger\Handler
 */
class HandlerFile extends Handler
{
    public $fileMode = 0775;
    protected $logContent = '';
    protected $logFile = '';

    /**
     * 10 * 1024 means that you can split files with 10M a file
     */
    protected $maxFileSize = 0;

    /**
     * log message style, you can expand it like this: <timestamp> [<requestid>][<ip>][<>][<level>][<category>]
     * <message>
     *
     * @var string
     */
    protected $logMessageFormat = '<timestamp> [-][-][-][<level>][<category>] <message>';

    /**
     * if 0, this handler will flush logs just when php script is shutdown, otherwise the handler will flush log every
     * time when log function is called
     *
     * @var int
     */
    protected $realTimeFlush = 0;

    /**
     * init
     */
    public function __construct()
    {
        //class init
        $this->init();
    }

    /**
     * init actions
     */
    public function init()
    {
        $this->registerFileLogShutDown();
    }

    public function enableRealTimeFlush()
    {
        $this->realTimeFlush = 1;
    }

    /**
     * set log file
     *
     * @param string $file
     */
    public function setLogFile($file)
    {
        if ( !empty( $file )) {
            $this->logFile = $file;
            $this->makeLogFile();
        }
        else {
            throw new \InvalidArgumentException('you can\'t set empty log path');
        }
    }

    /**
     * create log file if it is not exists
     *
     * @throws \Exception
     */
    protected function makeLogFile()
    {
        try {
            if ( !empty( $this->logFile )) {
                //create dir and file
                if ( !is_dir($logDir = dirname($this->logFile))) {
                    mkdir($logDir, $this->fileMode, true);
                    touch($this->logFile);
                }
                if ( !is_file($this->logFile)) {
                    touch($this->logFile);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * get the log file you setted before
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * set the log string style
     *
     * @param string $format : '<timestamp> [-][-][-][<level>][<category>] <message>';
     */
    public function setLogMessageFormat($format)
    {
        $this->logMessageFormat = $format;
    }

    /**
     * register a shutdown function so that the log string in memory can be flushed to file when process ends
     */
    protected function registerFileLogShutDown()
    {
        register_shutdown_function([$this, 'flushLog']);
    }

    /**
     * checks if this handler has enable realtime flush, if enabled ,the handler will flush log as real-time
     */
    protected function afterHanle()
    {
        if ($this->realTimeFlush === 1) {
            $this->flushLog();
        }
        parent::afterHanle();
    }

    /**
     * flush log string
     */
    public function flushLog()
    {
        if ( !empty( $this->logMessage )) {
            foreach ($this->logMessage as $message) {
                $this->logContent .= $this->pregLogContent($message) . PHP_EOL;
            }
            $this->flushToFile();
            $this->logMessage = [];
            $this->logContent = '';
        }
    }

    /**
     * recording to the self::logMessageFormat, convert log array to log string
     *
     * @param array $message
     *
     * @return string
     */
    protected function pregLogContent(array $message = [])
    {
        $messageStr = '';
        $replaceSearch = [];
        $replaceContent = [];
        foreach ($message as $messageKey => $messageVal) {
            $replaceSearch[] = '<' . $messageKey . '>';
            $replaceContent[] = $messageVal;
        }
        $messageStr = str_replace($replaceSearch, $replaceContent, $this->logMessageFormat);

        return $messageStr;
    }

    /**
     * flush log string to file
     */
    protected function flushToFile()
    {
        if ($this->maxFileSize === 0) {
            error_log($this->logContent, 3, $this->logFile);
        }
    }


}