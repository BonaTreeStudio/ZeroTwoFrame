<?php
/**
 * Simple trait providing single tone to class
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Traits;

trait SingleToneTrait {
    private static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $reflection     = new \ReflectionClass(__CLASS__);
            self::$instance = $reflection->newInstanceArgs(func_get_args());
        }
        return self::$instance;
    }

    final private function __clone(){}
    final private function __wakeup(){}
}