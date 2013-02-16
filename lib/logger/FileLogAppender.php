<?php
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
