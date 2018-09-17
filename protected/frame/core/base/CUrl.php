<?php

/**
 * Description of CUrl
 *
 * @author Alex
 */
class CUrl extends CAppComponent
{
    static private $_INSTANCE = NULL;
    protected $vars = [];
    protected $urlNesting = 0;
    protected $defaultController = 'default';
    protected $defaultAction = 'index';

    function __construct()
    {
        $conf = CApp::getInstance()->getConfig(CORE_CONFIG);
        $this->urlNesting = (int) (!empty($conf['url_nesting'])) ? $conf['url_nesting'] : $this->urlNesting;
        $this->defaultController = (int) (!empty($conf['default_controller'])) ? $conf['default_controller'] : $this->defaultController;
        $this->defaultAction = (int) (!empty($conf['default_action'])) ? $conf['default_action'] : $this->defaultAction;
        $this->parseUrl_modRewrite();
    }

    function parseUrl_modRewrite()
    {
        //Чистка URI
        $uri = preg_replace('#[a-z0-9]+\.[a-z0-9]+$#i', '', $_SERVER['REQUEST_URI']);

        $get_reqs = explode('/', $uri, 20);
        $this->vars['controller'] = (!empty($get_reqs[$this->urlNesting - 1])) ? $get_reqs[$this->urlNesting - 1] : $this->defaultController;
        $this->vars['action'] = (!empty($get_reqs[$this->urlNesting])) ? $get_reqs[$this->urlNesting] : $this->defaultController;
        for ($i = $this->urlNesting + 1; $i < count($get_reqs); $i++) {
            if (!empty($get_reqs[$i])) {
                $this->vars[$get_reqs[$i]] = (!empty($get_reqs[++$i])) ? $get_reqs[$i] : NULL;
            }
        }
    }

    public function getControllerParams()
    {
        $vars = $this->vars;
        return [
            'controller' => array_shift($vars),
            'action' => array_shift($vars),
            'args' => $vars,
        ];
    }

    /**
     * 
     * @return \self
     */
    public static function getInstance(): self
    {
        if (empty(self::$_INSTANCE)) {
            self::$_INSTANCE = new self();
        }
        return self::$_INSTANCE;
    }
}