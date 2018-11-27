<?php
/**
 * Created by PhpStorm.
 * User: eline
 * Date: 2018-11-27
 * Time: 13:36
 */

$router = new AltoRouter;


$router->map('GET', '/', '', 'home');


$match = $router->match();

if($match){
    require_once  __DIR__ . '/../controllers/BaseController.php';
    require_once  __DIR__ . '/../controllers/IndexController.php';

    $index = new \App\Controllers\IndexController();

    $index->show();

} else {
    header($_SERVER['SERVER_PROTOCOL'] . '404 Not Found');

    echo "Page not found!";
}