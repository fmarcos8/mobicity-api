<?php

use MobiCity\Controllers\BusController;

$router->get('/search-lines', [BusController::class, 'searchLines']);
$router->get('/search-line-position', [BusController::class, 'searchLinePosition']);