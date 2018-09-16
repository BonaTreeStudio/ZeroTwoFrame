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
class TestController extends CController
{

    public function actionIndex()
    {
        $criteria = new CDbSelectionCriteria();
        $criteria->compare('id', '2');
        $todo = Todos::model()->find($criteria);
//        /var_dump($todo);
        $todo->done = '1';
        $todo->save();
    }
}