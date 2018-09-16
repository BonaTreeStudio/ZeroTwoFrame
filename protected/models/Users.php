<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author Alex
 */
class Users extends CDbRecord
{
    public $id;
    public $login;
    public $password;
    public $fio;
    public static $_CURRENT_USER;

    protected function rules()
    {
        return [
            ['id', 'INT', 11, self::RULE_NO_DEFAULT, self::RULE_REQUIERED, self::RULE_AI],
            ['login, password, fio', 'VARCHAR', 255, self::RULE_NO_DEFAULT, self::RULE_REQUIERED],
        ];
    }

    protected function indexes()
    {
        return [
            [self::INDEX_PRIVATE_KEY, 'id'],
            [self::INDEX_UNICUE, 'login']
        ];
    }

    protected function relations()
    {
        return [];
    }

    public function table()
    {
        return 'users';
    }

    /**
     * Получает текущего авторизованного пользователя
     * @return Users
     */
    public static function getCurrent()
    {
        if (empty(self::$_CURRENT_USER)) {
            $current = CApp::getInstance()->session->getParam('currentUser', false);
            if (!empty($current)) {
                self::$_CURRENT_USER = self::model();
                self::$_CURRENT_USER->isNewRecord = false;
                self::$_CURRENT_USER->setAttributes($current);
            } else {
                self::$_CURRENT_USER = false;
            }
        }
        return self::$_CURRENT_USER;
    }

    public static function model(): self
    {
        return parent::model();
    }

    public static function signup($userData)
    {
        if (empty($userData) || empty($userData['login']) || empty($userData['password']) || empty($userData['fio'])) {
            throw new Exception("No user data passed!", 3);
        }
        $user = Users::model();
        $user->setAttributes($userData);
        $user->password = self::saltThePass($user->password);
        $user->save();
        self::auth();
    }

    public static function logout()
    {
        CApp::getInstance()->session->setParam('currentUser', NULL);
    }

    public static function auth()
    {
        $userData = CApp::getInstance()->request->getParam('userData', []);
        if (empty($userData) || empty($userData['login']) || empty($userData['password'])) {
            throw new Exception("No user data passed!", 3);
        }
        $userData['password'] = self::saltThePass($userData['password']);
        $user = self::model()->findByAttributes($userData);
        if (empty($user)) {
            throw new Exception("Login or Pass are incorect!", 13);
        }
        CApp::getInstance()->session->setParam('currentUser', $user->getAttributes());
        return self::getCurrent();
    }

    private static function saltThePass($pass)
    {
        return md5("key123123%%".$pass);
    }
}