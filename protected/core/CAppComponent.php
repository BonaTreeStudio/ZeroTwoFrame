<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CAppComponent
 *
 * @author Alex
 */
class CAppComponent
{

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