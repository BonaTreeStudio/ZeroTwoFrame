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
class CFreeAjaxJsonController extends CAjaxJsonController
{

    protected function apiFilter()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            throw new Exception('Query must be ajax!', 0);
        }
    }
}