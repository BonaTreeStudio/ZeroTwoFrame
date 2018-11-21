<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.10.18
 * Time: 1:00
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */

namespace Core\Parts\Render;

use Core\Base\СFileSystemDirectory;
use Core\Parts\Render\Exceptions\AssetNotFoundException;

class Asset
{
    const TYPE_CSS = 'css';
    const TYPE_JS = 'js';

    protected static $publishedAssets = [];

    use \Core\Traits\SingleToneKeyTrait;

    /**
     * @var СFileSystemDirectory
     */
    protected $sourceDir = NULL;
    protected $asset = '';
    protected $type = '';
    protected $params = [];

    protected $key = '';

    /**
     * @var СFileSystemDirectory
     */
    protected $runtimeAssetsDir = NULL;
    protected $dirKey = '';

    public function __construct($sourceDir, $asset, $type, $params)
    {
        $this->sourceDir = СFileSystemDirectory::getInstance($sourceDir);
        $this->asset = $this->sourceDir->getFile($asset, $type);
        if (empty($this->asset)) {
            throw new AssetNotFoundException($this->sourceDir->getFilePath($asset, $type));
        }
        $this->type = $type;
        $this->params = $params;

        $assetAssets = СFileSystemDirectory::getInstance(RUNTIME_PATH_CONFIG.'assets/');
        if (!$assetAssets->checkDir()) {
            $assetAssets->makeDir();
        }
        $this->dirKey = md5($this->sourceDir->getCurrentDir());
        $this->runtimeAssetsDir = СFileSystemDirectory::getInstance($assetAssets->getCurrentDir() . $this->dirKey . '/');
        if (!$this->runtimeAssetsDir->checkDir()) {
            $this->runtimeAssetsDir->makeDir();
        }
    }

    public function publish() {
        //Публикуем, только если ранее не публиковали
        if (empty(self::$publishedAssets[$this->getKey()])) {
            self::$publishedAssets[$this->getKey()] = $this;
            $assetTarget = $this->runtimeAssetsDir->getFile($this->asset->getFileName());
            if (empty($assetTarget) || ($assetTarget->getLastModifyTime() < $this->asset->getLastModifyTime())) {
                if (empty($assetTarget)) {
                    $assetTarget = $this->runtimeAssetsDir->getFileForce($this->asset->getFileName());
                }
                $assetTarget->wrightContent($this->asset->getContent());
            }
            echo $this->getPublishOutlet();
        }
    }

    protected function getPublishOutlet() {
        $method = 'getPublishOutlet_'.$this->type;
        if(method_exists($this, $method)) {
            return $this->$method();
        }
        return '';
    }

    protected function getPublishOutlet_css() {
        return '<link rel="stylesheet" href="./runtime/assets/'. $this->dirKey .'/'. $this->asset->getFileName() .'">';
    }

    protected function getPublishOutlet_js() {
        $init = (isset($this->params['init'])) ? $this->params['init'] : '';
        return '<script type="text/javascript" src="./runtime/assets/'. $this->dirKey .'/'. $this->asset->getFileName() .'" >' . $init . '</script>';
    }

    protected function getKey() {
        if (empty($this->key)) {
            $this->key = md5($this->asset->getFilePath());
        }
        return $this->key;
    }
}