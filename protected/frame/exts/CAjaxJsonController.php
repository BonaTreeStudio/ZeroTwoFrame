<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CAjaxJsonController
 *
 * @author Alex
 */
namespace Exts;

class CAjaxJsonController extends \Core\Base\CController
{
    /**
     *
     * @var CJsonAns $jsonResponce
     */
    protected $responce = NULL;

    protected function apiFilter()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            throw new Exception('Query must be ajax!', 0);
        }
        if (empty(Users::getCurrent())) {
            throw new Exception("You are not authorised!", 1);
        }
    }

    protected function initController()
    {
        parent::initController();
        $this->responce = CJsonAns::getInstance();
    }

    protected function beforeAction()
    {
        if (parent::beforeAction()) {
            return true;
        }
        return false;
    }

    public function runAction()
    {
        try {
            $this->apiFilter();
            parent::runAction();
        } catch (Exception $error) {
            $this->responce->setError($error->getMessage(), $error->getCode());
            $this->output();
        }
    }

    protected function output()
    {
        $this->responce->setHedder();
        echo $this->responce->output();
    }
}