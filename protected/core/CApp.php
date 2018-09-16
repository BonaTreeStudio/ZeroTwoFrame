<?php
require_once dirname(__FILE__).'/CLoader.php';

/**
 * Description of CApp
 *
 * @author Alex
 *
 * @property CSession $session Сессия
 */
class CApp
{
    private static $_INSTANCE = NULL;
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

    public static function getInstance($config = []): self
    {
        if (empty(self::$_INSTANCE)) {
            self::$_INSTANCE = new self($config);
        }
        return self::$_INSTANCE;
    }

    function __construct($config = [])
    {
        $this->config = $config;
    }

    private function setupCoreFunctions()
    {
        $this->session = new СSession($this->getConfig(CORE_CONFIG, 'session'));
        $this->request = new СRequest();
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
        CController::factory(CUrl::getInstance()->getControllerParams())->run();
    }

    public static function factory($config = [])
    {
        return self::getInstance($config);
    }
}