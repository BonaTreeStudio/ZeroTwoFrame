<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CController
 *
 * @author Alex
 */
class CController extends CAppComponent
{
    protected $action = NULL;
    protected $args = [];
    protected $output = '';

    public function __construct($setup)
    {
        $this->bind($setup);
    }

    protected function initController()
    {
        
    }

    protected function filter()
    {
        return true;
    }

    protected function beforeAction()
    {
        return true;
    }

    protected function afterAction()
    {
        return true;
    }

    protected function runAction()
    {
        $action = empty($this->action) ? 'index' : $this->action;
        $method = "action".ucfirst($action);
        if ($this->filter()) {
            if ($this->beforeAction()) {
                ob_start();
                if (method_exists($this, $method)) {
                    call_user_func_array([$this, $method], $this->args);
                }
                $this->output = ob_get_contents();
                ob_end_clean();
                $this->afterAction();
            }
            $this->output();
        }
    }

    public function run()
    {
        $this->initController();
        $this->runAction();
    }

    protected function output()
    {
        echo $this->output;
    }

    private static function getClassName($controller)
    {
        return ucfirst($controller).'Controller';
    }

    public static function factory($params): self
    {
        $controllerName = self::getClassName($params['controller']);
        if (class_exists($controllerName)) {
            return new $controllerName($params);
        }
        return false;
    }
}