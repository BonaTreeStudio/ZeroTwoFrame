<?php
/**
 * Simple trait providing single tone to class
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Traits;

trait SingleToneKeyTrait {
    private static $instances = [];

    public static function getInstance()
    {
        $args = func_get_args();
        $key = md5(serialize($args));
        if (!isset(self::$instances[$key])) {
            $reflection     = new \ReflectionClass(__CLASS__);
            self::$instances[$key] = $reflection->newInstanceArgs($args);
        }
        return self::$instances[$key];
    }

    final private function __clone(){}
    final private function __wakeup(){}
}