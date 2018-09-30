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
class AuthController extends \Core\Exts\CFreeAjaxJsonController
{

    public function actionLogin()
    {
        $user = Users::auth();
        if (empty($user)) {
            throw new Exception("Auth failed!", 12);
        }
        $this->responce->setCustom([
            'login' => $user->login,
            'fio' => $user->fio,
        ]);
    }

    public function actionLogout()
    {
        Users::logout();
    }
}