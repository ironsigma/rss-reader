<?php
/**
 * Abstract log appender.
 * @package com\hawkprime\logger
 */
abstract class LogAppender {
    protected $appenderLevel;

    /**
     * Constructor
     * If the level is NULL all messages passed will be logged.
     * The specific Logger can also filter messages.
     * @param integer $appenderLevel level constant
     */
    public function __construct($appenderLevel=null) {
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
        if (  $this->appenderLevel == null || $this->appenderLevel >= $messageLevel ) {
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
