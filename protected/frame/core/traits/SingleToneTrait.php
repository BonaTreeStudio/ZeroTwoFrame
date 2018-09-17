<?php
/**
 * Simple trait providing single tone to class
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Traits;

trait SingleToneTrait {
    protected static $_INSTANCE = NULL;

    public static function getInstance() {
        if (empty(self::$_INSTANCE)) {
            self::$_INSTANCE = new self();
        }
        return self::$_INSTANCE;
    }
}