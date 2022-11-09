<?php
declare ( strict_types = 1 );

namespace yzh52521\ThinkLaravel\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class LaravelModel extends Command
{
    private $type = 'laravel-model';

    protected function configure()
    {
        $this->setName( 'make:laravel-model' )
            ->addArgument( 'name',Argument::REQUIRED,"The name of the class" )
            ->setDescription( 'Create a new laravel model class' );
    }


    protected function execute(Input $input,Output $output)
    {
        $name      = trim( $input->getArgument( 'name' ) );
        $classname = $this->getClassName( $name );
        $pathname  = $this->getPathName( $classname );

        if (is_file( $pathname )) {
            $output->writeln( '<error>'.$this->type.':'.$classname.' already exists!</error>' );
            return false;
        }

        if (!is_dir( dirname( $pathname ) )) {
            mkdir( dirname( $pathname ),0755,true );
        }

        file_put_contents( $pathname,$this->buildClass( $classname ) );
        $output->writeln( '<info>'.$this->type.':'.$classname.' created successfully.</info>' );
    }


    protected function getPathName(string $name): string
    {
        $name = substr( $name,4 );
        return $this->app->getBasePath().ltrim( str_replace( '\\','/',$name ),'/' ).'.php';
    }


    protected function getClassName(string $name): string
    {
        if (strpos( $name,'\\' ) !== false) {
            return $name;
        }

        if (strpos( $name,'@' )) {
            [$app,$name] = explode( '@',$name );
        } else {
            $app = '';
        }

        if (strpos( $name,'/' ) !== false) {
            $name = str_replace( '/','\\',$name );
        }

        $ret = 'app'.( $app ? '\\'.$app : '' ).'\\model'.'\\'.$name;

        return $ret;
    }


    protected function buildClass(string $name): string
    {
        $stubPath  = __DIR__.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR;
        $stub      = file_get_contents( $stubPath.'model.stub' );
        $namespace = trim( implode( '\\',array_slice( explode( '\\',$name ),0,-1 ) ),'\\' );
        $class     = str_replace( $namespace.'\\','',$name );
        return str_replace( ['{%className%}','{%namespace%}'],[$class,$namespace,],$stub );
    }
}