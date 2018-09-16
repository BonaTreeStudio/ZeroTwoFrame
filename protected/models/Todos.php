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
class Todos extends CDbRecord
{
    public $id;
    public $owner;
    public $done;
    public $text;

    protected function rules()
    {
        return [
            ['id', 'INT', 11, self::RULE_NO_DEFAULT, self::RULE_REQUIERED, self::RULE_AI],
            ['owner', 'INT', 11, 0, self::RULE_REQUIERED],
            ['done', 'INT', 1, 0, self::RULE_REQUIERED],
            ['text', 'TEXT', self::RULE_ANY_LENGTH, '', self::RULE_REQUIERED],
        ];
    }

    protected function indexes()
    {
        return [
            [self::INDEX_PRIVATE_KEY, 'id'],
            [self::INDEX_FOREIGN_KEY, 'owner', 'users(id)', self::FOREIGN_KEY_ON_DELETE_CASCADE, self::FOREIGN_KEY_ON_UPDATE_NOTHING],
            [self::INDEX_SEARCH, 'done'],
        ];
    }

    protected function relations()
    {
        return [];
    }

    public function table()
    {
        return 'todos';
    }

    public static function model(): self
    {
        return parent::model();
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                if (empty($this->owner)) {
                    $this->owner = Users::getCurrent()->id;
                }
                if (empty($this->done)) {
                    $this->done = 0;
                }
            }
            return true;
        }
        return false;
    }
}