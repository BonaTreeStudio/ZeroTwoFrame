<?php
namespace Core;

require_once dirname(__FILE__).'/CLoader.php';
/**
 * Description of CApp
 *
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 *
 * @property CSession $session Session
 * @property СRequest $request Request
 */
class CApp {

    use Core\Traits\SingleToneTrait;

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