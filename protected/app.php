<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30.09.18
 * Time: 19:33
 * @author Alexander Galaktionov <alkadar.galaktionov@gmail.com>
 */
//Defining APP root
define('APP_ROOT', dirname(__FILE__).'/');

//Geting config
$config = require(APP_ROOT.'config/main.php');

//Including Core Loader
require dirname(__FILE__).'/frame/core/CLoader.php';
//App start
\Core\CApp::factory($config)->run();