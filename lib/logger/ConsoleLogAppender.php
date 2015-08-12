<?php
/**
 * Minimal console appender.
 * Will just echo the timestamp, label and message.
 * @package com\hawkprime\logger
 */
class ConsoleLogAppender extends LogAppender {
    public function logMessage($levelDescription, $label, $message) {
        echo $this->timeStamp() ." | $levelDescription | $label | $message\n";
    }
}
