<?php

#Start session if it is not already started

if(!isset($_SESSION)) session_start();

//Load environment variable

require_once __DIR__ . '/../app/config/_env.php';

require_once __DIR__ . '/../app/routing/routes.php';