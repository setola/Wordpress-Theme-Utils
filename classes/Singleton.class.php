<?php

/**
 * This class describes a Singleton object
 * @author http://stackoverflow.com/questions/3126130/extending-singletons-in-php
 * @version 1.0.0
 * @package classes
 */
abstract class Singleton{

    /**
     * Instantiate an object only inside the children classes
     */
    protected function __construct(){}

    /**
     * Retrieves the instance of the object of the called class
     * @return mixed
     */
    final public static function getInstance(){
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass();
        }

        return $instances[$calledClass];
    }

    /**
     * Avoid cloning of singletons
     */
    final private function __clone(){}
}