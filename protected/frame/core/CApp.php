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

class CApp {

    use \Core\Traits\SingleToneTrait;

    /**
     *
     * @var СSession
     */
    public $session = NULL;
    /**
     *
     * @var СRequest
     */
    public $request = NULL;
    protected $config = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    private function setupCoreFunctions()
    {
        $this->session = new \Core\Parts\Server\СSession($this->getConfig(CORE_CONFIG, 'session'));
        $this->request = new \Core\Parts\Server\СRequest();
    }

    public function getConfig()
    {
        $sections = func_get_args();
        $section = array_shift($sections);
        $config = $this->config[$section] ?? [];
        if (!empty($section)) {
            foreach ($sections as $section) {
                if (empty($config[$section])) {
                    $config = [];
                    break;
                }
                $config = $config[$section];
            }
        }
        return $config ?? [];
    }

    function run()
    {
        $this->setupCoreFunctions();
        $urlParams = \Core\Base\CUrl::getInstance()->getControllerParams();
        if ($urlParams['module'] !== 'core') {
            $controller = \Core\CAppModule::factory($urlParams['module'] ,$this->config[MODULES][$urlParams['module']], $this->config)->run();
        } else {
            $urlParams['module'] = $this;
            $controller = \Core\Base\CController::factory($urlParams);
        }
        if (empty($controller)) {
            throw new \Exception('Controller ' . $urlParams['controller'] . ' not found', 404);
        }
        $controller->run();
    }

    function getModuleViewPath() {
        $urlParams = \Core\Base\CUrl::getInstance()->getControllerParams();
        return APP_ROOT . 'views/';
    }

    public static function factory($config = [])
    {
        return self::getInstance($config);
    }
}