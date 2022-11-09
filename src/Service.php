<?php
declare ( strict_types = 1 );

namespace yzh52521\ThinkLaravel;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Jenssegers\Mongodb\Connection as MongodbConnection;
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
        $config      = config( 'laravelorm',[] );
        $connections = $config['connections'] ?? [];
        if (!$connections) {
            return;
        }

        $capsule = new Manager;
        $capsule->getDatabaseManager()->extend( 'mongodb',function ($config,$name) {
            $config['name'] = $name;
            return new MongodbConnection( $config );
        } );

        $default = $config['default'] ?? false;
        if ($default) {
            $default_config = $connections[$config['default']];
            $capsule->addConnection( $default_config );
        }

        foreach ( $connections as $name => $config ) {
            $capsule->addConnection( $config,$name );
        }

        $capsule->setEventDispatcher( new Dispatcher( new Container ) );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}