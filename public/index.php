<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap The Application
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app->handleRequest(Request::capture());
