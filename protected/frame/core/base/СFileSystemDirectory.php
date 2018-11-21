<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.10.18
 * Time: 1:18
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */

namespace Core\Base;

use \Core\Base\СFileSystemFile;

class СFileSystemDirectory
{
    use \Core\Traits\SingleToneKeyTrait;
    use \Core\Traits\BindTrait;

    protected $dir = '';

    public function __construct($dir) {
        $this->dir = $dir;
    }

    public function getCurrentDir() {
        return $this->dir;
    }

    public function getFilePath($name, $ext = '') {
        return $this->dir.$name.((empty($ext)) ? '' : '.'.$ext);
    }

    public function checkDir() {
        return file_exists($this->getCurrentDir()) && is_dir($this->getCurrentDir());
    }

    public function makeDir() {
        if (!$this->checkDir()) {
            mkdir($this->getCurrentDir());
        }
    }

    public function checkFile($name, $ext = '') {
        return file_exists($this->getFilePath($name, $ext));
    }

    /**
     * @param $name
     * @param $ext
     * @return bool | СFileSystemFile
     */
    public function getFile($name, $ext = '') {
        if ($this->checkFile($name, $ext)) {
            return СFileSystemFile::getInstance($this->getFilePath($name, $ext));
        }
        return false;
    }

    /**
     * @param $name
     * @param $ext
     * @return СFileSystemFile
     */
    public function getFileForce($name, $ext = '') {
        $file = СFileSystemFile::getInstance($this->getFilePath($name, $ext));
        if (!$this->checkFile($name, $ext)) {
            $file->touch();
        }
        return $file;
    }
}