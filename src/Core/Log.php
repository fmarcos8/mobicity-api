<?php

namespace MobiCity\Core;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;

class Log
{
    private static $logger = null;

    private static function getLogger()
    {
        if (self::$logger == null) {
            self::$logger = new Logger("api_logger");
            $handler = new StreamHandler(__DIR__.'/../../logs/api.log');            
            $output = "[%datetime%] %channel%.%level_name%: %message%\n";
            $formatter = new LineFormatter($output, null, true, true);
            $handler->setFormatter($formatter);

            self::$logger->pushHandler($handler);
        }

        return self::$logger;
    }

    private static function format($var)
    {
        if (is_array($var) || is_object($var)) {
            return print_r($var, true);
        }

        return $var;
    }

    public static function debug(mixed $context): void
    {
        $data = self::format($context);
        self::getLogger()->debug($data);
    }

    public static function info(mixed $context): void
    {
        $data = self::format($context);
        self::getLogger()->info($data);
    }

    public static function error(mixed $context): void
    {
        $data = self::format($context);
        self::getLogger()->error($data);
    }

    public static function logException(Throwable $throwable) 
    {
        $formatter = self::formatException($throwable);
        self::getLogger()->error($formatter);
    }

    public static function formatException(Throwable $throwable): string
    {
        $result = sprintf("%s: %s in %s:%d\n[Stack trace]:%s\n",
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTraceAsString()
        );

        return $result;
    }
}