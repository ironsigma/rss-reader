<?php
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
