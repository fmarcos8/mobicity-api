<?php
require __DIR__.'/../error_handler.php';
require __DIR__.'/../vendor/autoload.php';

use MobiCity\Core\Application;

$app = new Application();

$app->run();