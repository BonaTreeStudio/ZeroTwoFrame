<?php
namespace Core;

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
    private $config = [];

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
        \Core\Base\CController::factory(\Core\Base\CUrl::getInstance()->getControllerParams())->run();
    }

    public static function factory($config = [])
    {
        return self::getInstance($config);
    }
}