<?php
define('APP_PATH', substr(__DIR__, 0, -5));
include('../lib/AutoLoader.php');

LogFacility::setRootLoggerLevel(Logger::WARN);
LogFacility::addAppender(new ConsoleLogAppender());
