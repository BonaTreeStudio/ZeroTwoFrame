<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of СRequest
 *
 * @author Alex
 */
class СRequest extends CAppRequesterComponent
{

    public function getParam($name, $default = NULL)
    {
        return $_REQUEST[$name] ?? $default;
    }

    public function setParam($name, $value, $default = NULL)
    {
        $_REQUEST[$name] = $value ?? $default;
    }
}