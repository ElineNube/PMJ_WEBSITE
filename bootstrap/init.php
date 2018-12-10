<?php
/**
 * Start session if not already started
 */

ini_set('display_errors', 1);


if(!isset($_SESSION)) session_start();

//load environment variable
require_once __DIR__ . '/../app/config/_env.php';

////instantiate database class

new \App\Classes\Database();

//load routes
require_once __DIR__ . '/../app/routing/routes.php';

// create a new RouteDispatcher
new \App\RouteDispatcher($router);