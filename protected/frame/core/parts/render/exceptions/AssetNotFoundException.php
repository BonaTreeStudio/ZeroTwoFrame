<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11.11.18
 * Time: 21:51
 */

namespace Core\Parts\Render\Exceptions;

use Throwable;

class AssetNotFoundException extends \Exception
{
    public function __construct(string $asset = "", Throwable $previous = null)
    {
        parent::__construct("[Ассет {$asset} не найден!]", 404, $previous);
    }
}