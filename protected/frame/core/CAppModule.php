<?php
/**
 * Description of CApp
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 *
 * @property CSession $session Session
 * @property СRequest $request Request
 */
namespace Core;

class CAppModule {

    use \Core\Traits\SingleToneTrait;

    protected $moduleSettings = [];

    function __construct(array $config = [], $appConfig = [])
    {
        $this->moduleSettings = $config;
        foreach ($appConfig as $key => $item) {
            if (empty($this->moduleSettings[$key])) {
                $this->moduleSettings[$key] = $item;
            }
        }
    }


    function run() {
        $urlParams = \Core\Base\CUrl::getInstance()->getControllerParams();
        \CLoader::setModulePriority($urlParams['module'], $this->moduleSettings['path']);
        $urlParams['module'] = $this;
        $controller = \Core\Base\CController::factory($urlParams);
        return $controller;
    }

    function getModuleViewPath() {
        $urlParams = \Core\Base\CUrl::getInstance()->getControllerParams();
        return APP_ROOT . 'modules/'. $urlParams['module'] . '/views/';
    }

    public static function factory($module, $moduleConfig = [], $appConfig = [])
    {
        //var_dump($module, $moduleConfig, $appConfig);
        $module = ucfirst($module).'Module';
        self::$instance = new $module($moduleConfig, $appConfig);
        return self::getInstance();
    }
}