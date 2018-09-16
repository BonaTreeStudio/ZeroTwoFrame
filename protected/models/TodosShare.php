<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TodosShare
 *
 * @author Alex
 */
class TodosShare extends CDbRecord
{
    public $owner;
    public $shared_to;
    public $can_edit;

    protected function rules()
    {
        return [
            ['owner, shared_to', 'INT', 11, self::RULE_NO_DEFAULT, self::RULE_REQUIERED],
            ['can_edit', 'INT', 1, 0, self::RULE_REQUIERED],
        ];
    }

    protected function indexes()
    {
        return [
            [self::INDEX_PRIVATE_KEY, 'owner, shared_to'],
            [self::INDEX_FOREIGN_KEY, 'owner', 'users(id)', self::FOREIGN_KEY_ON_DELETE_CASCADE, self::FOREIGN_KEY_ON_UPDATE_NOTHING],
            [self::INDEX_FOREIGN_KEY, 'shared_to', 'users(id)', self::FOREIGN_KEY_ON_DELETE_CASCADE, self::FOREIGN_KEY_ON_UPDATE_NOTHING],
        ];
    }

    protected function relations()
    {
        return [];
    }

    public function table()
    {
        return 'todos_share';
    }

    public static function model(): self
    {
        return parent::model();
    }
}