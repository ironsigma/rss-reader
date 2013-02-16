<?php

LogFacility::addAppender(new ConsoleLogAppender(Logger::INFO));
LogFacility::addAppender(new FileLogAppender(Logger::TRACE, __DIR__ .'/log/updater.log'));

LogFacility::setRootLoggerLevel(Logger::TRACE);
//LogFacility::setLoggerLevel('DbService', Logger::TRACE);

DbService::setDatabase(__DIR__ .'/reader.sqlite3');
