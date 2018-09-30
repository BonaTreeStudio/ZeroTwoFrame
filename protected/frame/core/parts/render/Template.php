<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.10.18
 * Time: 0:45
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */

namespace Core\Parts\Render;


class Template
{
    use \Core\Traits\BindTrait;
    use \Core\Traits\FieldRequestTrait;

    protected $sourceDir = '';
    protected $layout = NULL;
    protected $fileSystem = '';

    protected function __construct($fields)
    {
        $this->bind($fields);
    }

    public function render($template, $params = [], $returnOutput = false, $processAssets = true) {

    }

    public function renderPartial($template, $params = [], $returnOutput = false, $processAssets = true) {

    }

    public static function factory($sourceDir, $layout = NULL) : self {
        return new self([
            'sourceDir' => $sourceDir,
            'layout' => $layout,
        ]);
    }
}