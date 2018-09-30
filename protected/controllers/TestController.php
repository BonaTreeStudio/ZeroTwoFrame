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
class TestController extends \Core\Base\CController
{
    public function actionIndex()
    {
        echo "123123";
        $criteria = new \Core\Parts\Database\CDbSelectionCriteria();
        $criteria->compare('id', '2');
        $criteria->compare('name', '10');
        var_dump($criteria->getCriteria());
    }
}