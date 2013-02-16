<?php

LogFacility::setRootLoggerLevel(Logger::INFO);
LogFacility::addAppender(new FileLogAppender(Logger::INFO, __DIR__ .'/log/updater.log'));

DbService::setDatabase(__DIR__ .'/reader.sqlite3');
