<?php
/**
 * Created by PhpStorm.
 * User: eline
 * Date: 2018-11-27
 * Time: 10:27
 */

define('BASE_PATH', realpath(__DIR__.'/../../'));

require_once __DIR__.'/../../vendor/autoload.php';

$dotENV = new \Dotenv\Dotenv(BASE_PATH);
$dotENV->load();