<?php
/**
 * Created by PhpStorm.
 * User: eline
 * Date: 2018-11-27
 * Time: 13:36
 */

$router = new AltoRouter();

$router->map('GET', '/', 'App\Controllers\IndexController@show', 'home');
