<?php
/**
 * Logger class
 * @package com\hawkprime\logger
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
    public static function getLevelConstant($level) {
        switch ( $level) {
        case 'ERROR': return self::ERROR;
        case 'WARN':  return self::WARN;
        case 'INFO':  return self::INFO;
        case 'DEBUG': return self::DEBUG;
        case 'TRACE': return self::TRACE;
        }
        return null;
    }
}
