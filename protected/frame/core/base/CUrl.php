<?php

/**
 * Description of CUrl
 *
 * @author Alex
 */
namespace Core\Base;

class CUrl
{
    use \Core\Traits\SingleToneTrait;

    protected $vars = [];
    protected $urlNesting = 0;
    protected $defaultModule = 'core';
    protected $defaultController = 'default';
    protected $defaultAction = 'index';

    public function __construct()
    {
        $conf = \Core\CApp::getInstance()->getConfig(CORE_CONFIG);
        $this->urlNesting = (int)(!empty($conf['url_nesting'])) ? $conf['url_nesting'] : $this->urlNesting;
        $this->defaultModule = (int)(!empty($conf['default_module'])) ? $conf['default_module'] : $this->defaultModule;
        $this->defaultController = (int)(!empty($conf['default_controller'])) ? $conf['default_controller'] : $this->defaultController;
        $this->defaultAction = (int)(!empty($conf['default_action'])) ? $conf['default_action'] : $this->defaultAction;
        $this->parseUrl_modRewrite();
    }

    function parseUrl_modRewrite()
    {
        //Чистка URI
        $uri = preg_replace('#[a-z0-9]+\.[a-z0-9]+$#i', '', $_SERVER['REQUEST_URI']);

        $get_reqs = explode('/', $uri, 20);
        $desc = $this->urlNesting;

        $this->vars['module'] = (!empty($get_reqs[$desc])) ? $get_reqs[$desc] : $this->defaultModule;
        if (class_exists(ucfirst($this->vars['module']).'Module', true)) {
            $desc++;
        } else {
            $this->vars['module'] = $this->defaultModule;
        }
        $this->vars['controller'] = (!empty($get_reqs[$desc])) ? $get_reqs[$desc] : $this->defaultController;
        $desc++;
        $this->vars['action'] = (!empty($get_reqs[$desc])) ? $get_reqs[$desc] : $this->defaultAction;
        for ($i = $desc + 1; $i < count($get_reqs); $i++) {
            if (!empty($get_reqs[$i])) {
                $this->vars[$get_reqs[$i]] = (!empty($get_reqs[++$i])) ? $get_reqs[$i] : NULL;
            }
        }
    }

    public function getControllerParams()
    {
        $vars = $this->vars;
        //var_dump($vars);
        return [
            'module' => array_shift($vars),
            'controller' => array_shift($vars),
            'action' => array_shift($vars),
            'args' => $vars,
        ];
    }
}