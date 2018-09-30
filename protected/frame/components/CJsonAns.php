<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CJson
 *
 * @author Alex
 */
namespace Core\Components;

class CJsonAns extends CAppComponent
{
    use \Core\Traits\SingleToneTrait;

    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';

    const ANS_MESSAGE = 'message';
    const ANS_DATA = 'data';

    protected $ans = [];

    public function __construct()
    {
        $this->setStatus(self::STATUS_OK);
    }

    public function setStatus($status): self
    {
        $this->ans['status'] = $status;
        return $this;
    }

    public function setError($message, $code = 0): self
    {
        $this->setStatus(self::STATUS_ERROR);
        return $this->setCustom([
                'message' => $message,
                'code' => $code,
        ]);
    }

    public function setCustom($values): self
    {
        return $this->bindArray('ans', $values);
    }

    public function setHedder()
    {

    }

    public function output(): string
    {
        return json_encode($this->ans);
    }
}