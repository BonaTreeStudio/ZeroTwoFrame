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
namespace Core\Parts\Server;

class СRequest
{
    use \Core\Traits\SingleToneTrait;
    use \Core\Traits\FieldRequestTrait;

    public function getParam($name, $default = NULL)
    {
        return $_REQUEST[$name] ?? $default;
    }

    public function setParam($name, $value, $default = NULL)
    {
        $_REQUEST[$name] = $value ?? $default;
    }
}