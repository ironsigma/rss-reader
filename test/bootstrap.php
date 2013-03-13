<?php
define('APP_PATH', substr(__DIR__, 0, -5));
define('APP_ADDL_INC_PATHS', APP_PATH.'/lib/db');

include('../lib/AutoLoader.php');

LogFacility::setRootLoggerLevel(Logger::WARN);
LogFacility::addAppender(new ConsoleLogAppender());
