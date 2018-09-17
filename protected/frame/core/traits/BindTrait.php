<?php
/**
 * Simple trait providing bind methods
 * in continues style
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
namespace Core\Traits;

trait BindTrait {
    protected function bind($fields): self
    {
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
        return $this;
    }

    protected function bindArray($property, $fields): self
    {
        if (property_exists($this, $property)) {
            foreach ($fields as $field => $value) {
                $this->$property[$field] = $value;
            }
        }
        return $this;
    }
}