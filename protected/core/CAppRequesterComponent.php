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
class CAppRequesterComponent extends CAppComponent
{

    public function __get($name)
    {
        return $this->getParam($name);
    }

    public function __set($name, $value)
    {
        $this->setParam($name, $value);
    }
}