<?php
declare ( strict_types = 1 );

namespace yzh52521\ThinkLaravel;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use think\facade\Config;
use yzh52521\ThinkLaravel\command\LaravelModel;

class Service extends \think\Service
{
    public function register()
    {
        $this->connect();

        $this->commands( [
            LaravelModel::class
        ] );
    }

    private function connect()
    {
        $capsule = new Manager;
        $capsule->addConnection( [
            'driver'    => Config::get( 'laravelorm.driver' ),
            'host'      => Config::get( 'laravelorm.host' ),
            'database'  => Config::get( 'laravelorm.database' ),
            'username'  => Config::get( 'laravelorm.username' ),
            'password'  => Config::get( 'laravelorm.password' ),
            'charset'   => Config::get( 'laravelorm.charset' ),
            'collation' => Config::get( 'laravelorm.collation' ),
            'prefix'    => Config::get( 'laravelorm.prefix' ),
        ] );

        $capsule->setEventDispatcher( new Dispatcher( new Container ) );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}