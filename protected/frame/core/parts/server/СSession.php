<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of СSession
 *
 * @author Alex
 */
namespace Core\Parts\Server;

class СSession
{
    use \Core\Traits\SingleToneTrait;
    use \Core\Traits\FieldRequestTrait;

    public function __construct($options)
    {
        session_start($options);
    }

    public function getParam($name, $default = NULL)
    {
        return $_SESSION[$name] ?? $default;
    }

    public function setParam($name, $value, $default = NULL)
    {
        $_SESSION[$name] = $value ?? $default;
    }
}