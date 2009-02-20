<?php

class Mutateme_Framework
{

    public static function autoload($class)
    {
        if (substr($class, 0, 12) != 'Mutateme_') {
            return false;
        }
        $path = dirname(dirname(__FILE__));
        include_once $path . '/' . str_replace('_', '/', $class) . '.php';
    }

}

spl_autoload_register(array('Mutateme_Framework','autoload'));
