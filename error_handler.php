<?php

use MobiCity\Core\Log;

set_exception_handler(function(Throwable $throwable) {
    Log::logException($throwable);
    http_response_code(500);
    echo "Server Error!";
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    Log::error("$errstr in $errfile:$errline");
    return true;
}); 

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL) {
        Log::error("{$error['message']} in {$error['file']}:{$error['line']}");
    }
});