<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 11.11.18
 * Time: 21:51
 */

namespace Core\Parts\Render\Exceptions;

use Throwable;

class TemplateNotFoundException extends \Exception
{
    public function __construct(string $template = "", Throwable $previous = null)
    {
        parent::__construct("[Шаблон {$template} не найден!]", 404, $previous);
    }
}