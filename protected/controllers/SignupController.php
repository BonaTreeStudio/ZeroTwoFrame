<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthController
 *
 * @author Alex
 */
class SignupController extends CFreeAjaxJsonController
{

    public function actionSignup()
    {
        $userData = CApp::getInstance()->request->getParam('userData', []);
        Users::signup($userData);
        $user = Users::getCurrent();
        if (empty($user)) {
            throw new Exception("signup auth failed", 14);
        }
        $this->responce->setCustom([
            'login' => $user->login,
            'fio' => $user->fio,
        ]);
    }
}