<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.10.18
 * Time: 1:18
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */

namespace Core\Base;

use \Core\Base\СFileSystemDirectory;

class СFileSystemFile
{
    const EXT_PHP = 'php';

    use \Core\Traits\SingleToneKeyTrait;
    use \Core\Traits\BindTrait;

    protected $fileName = '';
    /**
     * @var СFileSystemDirectory
     */
    protected $fileDir = NULL;
    protected $filePath = '';

    public function __construct($filePath) {
        $explPath = explode('/', $filePath);
        $this->fileName = array_pop($explPath);
        $this->fileDir = СFileSystemDirectory::getInstance(implode($explPath) . '/');
        $this->filePath = $filePath;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getFileDir() {
        return $this->fileDir();
    }

    public function getLastModifyTime() {
        return filemtime($this->filePath);
    }

    public function wrightContent($content, $flags = 0) {
        file_put_contents($this->getFilePath(), $content, $flags);
    }

    public function getContent() {
        return file_get_contents($this->getFilePath());
    }

    public function touch() {
        touch($this->getFilePath());
    }
}