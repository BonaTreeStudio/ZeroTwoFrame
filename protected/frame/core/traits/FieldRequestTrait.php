<?php
/**
 * Field request/setter trait
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Traits;

trait FieldRequestTrait {
    public function __get($name) {
        return $this->getParam($name);
    }

    public function __set($name, $value) {
        $this->setParam($name, $value);
    }
}