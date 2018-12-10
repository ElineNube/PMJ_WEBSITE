<?php
/**
 * Created by PhpStorm.
 * User: eline
 * Date: 2018-12-10
 * Time: 19:26
 */

namespace App\Classes;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{

    public function  __construct()
    {
        $db = new Capsule;
        $db->addConnection([
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_NAME'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''

        ]);

        $db->setAsGlobal();

        $db->bootEloquent();
    }

}