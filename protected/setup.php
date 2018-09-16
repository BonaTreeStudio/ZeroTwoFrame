<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__).'/core/CApp.php';
$config = require(dirname(__FILE__).'/config/main.php');
CApp::factory($config);
Users::model()->setup();
Todos::model()->setup();
TodosShare::model()->setup();


