<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CTemplateController
 *
 * @author Alex
 */
namespace Exts;

use Core\Parts\Render\Template;

class CTemplateController extends \Core\Base\CController
{
    protected $layout = NULL;

    /**
     *
     * @var \Core\Parts\Render\Template $template
     */
    protected $template = NULL;

    protected function initController()
    {
        parent::initController();
        $path = $this->module->getModuleViewPath() . str_replace('Controller', '',  lcfirst(get_class($this))) . '/';
        $this->template = Template::factory($path, $this->layout);
    }

    protected function beforeAction()
    {
        if (parent::beforeAction()) {
            return true;
        }
        return false;
    }

    public function runAction()
    {
        try {
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
                $this->template->setBgOutput($this->output);
                $this->output();
            }
        } catch (Exception $error) {
            throw $error;
        }
    }

    protected function output()
    {
        echo $this->template->render($this->action);
    }
}