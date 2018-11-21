<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.10.18
 * Time: 0:45
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */

namespace Core\Parts\Render;

use Core\Base\СFileSystemDirectory;
use Core\Base\СFileSystemFile;
use Core\Parts\Render\Exceptions\TemplateNotFoundException;

class Template
{
    use \Core\Traits\BindTrait;
    use \Core\Traits\FieldRequestTrait;

    /**
     * @var string | СFileSystemDirectory
     */
    protected $sourceDir = '';

    protected $layout = NULL;

    protected $bgOutput = '';

    protected $renderedTemplate = '';

    protected function __construct($fields)
    {
        $this->bind($fields);
        $this->sourceDir = СFileSystemDirectory::getInstance($this->sourceDir);
    }

    public function publishAsset($asset, $type, $assetParams = []) {
        $oAsset = Asset::getInstance($this->sourceDir->getCurrentDir(). '_' . $this->renderedTemplate.'-assets/', $asset, $type, $assetParams);
        $oAsset->publish();
    }

    public function setBgOutput($output) {
        $this->bgOutput = $output;
    }

    public function render($template, $params = [], $returnOutput = false, $processAssets = true) {
        $output = $this->renderPartial($template, $params, true, true);
        if (isset($this->layout)) {
            $layout = Template::factory(APP_ROOT.'layouts/');
            $output = $layout->render($this->layout, [
                'content' => $output,
                'debugOutput' => $this->bgOutput,
            ], true, true);
        }
        if ($returnOutput) {
            return $output;
        }
        echo $output;
    }

    public function renderTemplatePart($part, $params = [], $returnOutput = false, $processAssets = true) {
        $partTemplate = Template::factory($this->sourceDir->getCurrentDir() . '_' . $this->renderedTemplate.'/');
        $output = $partTemplate->renderPartial($part, $params, true, $processAssets);
        if ($returnOutput) {
            return $output;
        }
        echo $output;
    }

    public function renderPartial($template, $params = [], $returnOutput = false, $processAssets = true) {
        $templateFile = $this->sourceDir->getFilePath($template, СFileSystemFile::EXT_PHP);
        if (!$this->sourceDir->checkFile($template, СFileSystemFile::EXT_PHP)) {
            throw new TemplateNotFoundException($templateFile);
        }
        ob_start();
            $this->renderedTemplate = $template;
            foreach ($params as $name => $val){
                $$name = $val;
            }
            require_once $templateFile;
            $this->renderedTemplate = '';
        $output = ob_get_contents();
        ob_end_clean();
        if ($returnOutput) {
            return $output;
        }
        echo $output;
    }

    /**
     * @param $sourceDir
     * @param null $layout
     * @return Template
     */
    public static function factory($sourceDir, $layout = NULL) : self {
        return new self([
            'sourceDir' => $sourceDir,
            'layout' => $layout,
        ]);
    }
}