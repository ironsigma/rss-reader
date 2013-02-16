<?php
/**
 * Abstract log appender.
 *
 * Lightweight logging class.
 *
 * Usage:
 *
 *     // Create appenders
 *     Logger::addAppender(new ConsoleLogAppender(Logger::WARN));
 *     Logger::addAppender(new FileLogAppender(Logger::DEBUG, '/var/log/logger.log'));
 *     LogFacility::setRootLoggerLevel(Logger::DEBUG);
 *     LogFacility::setLoggerLevel('database.php', Logger::ERROR);
 *
 *     // Get logger
 *     $log = Logger::getLogger('database.php');
 *     $log->debug('debug');
 *     $log->info('info');
 *     $log->warn('warn');
 *     $log->error('error');
 *
 */
abstract class LogAppender {
    protected $appenderLevel;

    /**
     * Constructor
     * @param integer $appenderLevel level constant
     */
    public function __construct($appenderLevel) {
        $this->appenderLevel = $appenderLevel;
    }

    /**
     * Interface for the Logger class.
     * This will determine if the message is fit for
     * loging and pass it to the child class if neccesary.
     * @param integer $messageLevel level constant
     * @param string $label The logger label
     * @param string $message The message to log
     */
    public function process($messageLevel, $label, $message) {
        if (  $this->appenderLevel >= $messageLevel ) {
            $this->logMessage(Logger::getLevelString($messageLevel), $label, $message);
        }
    }

    /**
     * Prefix message with timestamp.
     */
    protected function timeStamp() {
        return date('Y-m-d H:i:s O');
    }

    /**
     * This does the actual logging for concrete classes.
     * @param integer $levelDescription level constant as a string
     * @param string $label The logger label
     * @param string $message The message to log
     */
    abstract protected function logMessage($levelDescription, $label, $message);
}

/**
 * Minimal console appender.
 * Will just echo the timestamp, label and message.
 */
class ConsoleLogAppender extends LogAppender {
    public function logMessage($levelDescription, $label, $message) {
        echo $this->timeStamp() ." | $levelDescription | $label | $message\n";
    }
}

/**
 * Minimal File appender.
 * Will open a file and write the timestamp, label and message to it.
 */
class FileLogAppender extends LogAppender {
    protected $fileHandle = null;
    public function __construct($level, $file) {
        // init parent and open file
        parent::__construct($level);
        $this->fileHandle = fopen($file, 'ab');
    }
    public function __destruct() {
        // close file if neccesary
        if ( $this->fileHandle ) {
            fclose($this->fileHandle);
        }
    }
    public function logMessage($levelDescription, $label, $message) {
        // if file is open write entry
        if ( $this->fileHandle ) {
            fwrite($this->fileHandle, $this->timeStamp() ." | $levelDescription | $label | $message\n");
        }
    }
}

/**
 * Logger class
 */
class Logger {
    // level
    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;
    const DEBUG = 3;
    const TRACE = 4;

    // logger label
    protected $label;
    protected $loggerLevel;

    public function __construct($loggerLevel, $label) {
        $this->loggerLevel = $loggerLevel;
        $this->label = $label;
    }
    public function error($message) {
        $this->log(self::ERROR, $message);
    }
    public function warn($message) {
        $this->log(self::WARN, $message);
    }
    public function info($message) {
        $this->log(self::INFO, $message);
    }
    public function debug($message) {
        $this->log(self::DEBUG, $message);
    }
    public function trace($message) {
        $this->log(self::TRACE, $message);
    }
    public function log($messageLevel, $message) {
        if (  $this->loggerLevel >= $messageLevel ) {
            LogFacility::processMessage($messageLevel, $this->label, $message);
        }
    }
    public function setLevel($level) {
        $this->loggerLevel = $level;
    }
    public static function getLevelString($levelConstant) {
        switch ( $levelConstant ) {
        case self::ERROR: return 'ERROR';
        case self::WARN: return 'WARN';
        case self::INFO: return 'INFO';
        case self::DEBUG: return 'DEBUG';
        case self::TRACE: return 'TRACE';
        }
        return 'UNKNOWN';
    }
}

class LogFacility {
    // loggers and appenders
    protected static $loggerList = array();
    protected static $loggerLevelList = array();
    protected static $appenderList = array();
    protected static $rootLevel = Logger::ERROR;

    /**
     * Get a new logger.
     * @param string $label Label to use for logging.
     */
    public static function getLogger($label) {
        // get logger level
        $loggerLevel = self::$rootLevel;
        if ( array_key_exists($label, self::$loggerLevelList) ) {
            $loggerLevel = self::$loggerLevelList[$label];
        }

        // create/fetch logger
        if ( array_key_exists($label, self::$loggerList) ) {
            self::$loggerList[$label]->setLevel($loggerLevel);
        } else {
            self::$loggerList[$label] = new Logger($loggerLevel, $label);
        }

        // done
        return self::$loggerList[$label];
    }

    /**
     * Add new appender
     * @param LogAppender $appender Appender to add
     */
    public static function addAppender(LogAppender $appender) {
        self::$appenderList[] = $appender;
    }

    public static function setRootLoggerLevel($level) {
        self::$rootLevel = $level;
    }

    public static function setLoggerLevel($label, $level) {
        self::$loggerLevelList[$label] = $level;
        if ( array_key_exists($label, self::$loggerList) ) {
            self::$loggerList[$label]->setLevel($level);
        }
    }

    /**
     * Log message to appender.
     * This will be called by the individual loggers.
     * @param string $level Message level.
     * @param string $label Label to use.
     * @param string $message Message to log.
     */
    public static function processMessage($messageLevel, $label, $message) {
        foreach ( self::$appenderList as $appender ) {
            $appender->process($messageLevel, $label, $message);
        }
    }
}
