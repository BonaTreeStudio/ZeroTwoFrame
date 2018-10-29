<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Main cofig const
define('LOADER_PATH_CONFIG', 'path');

define('DATABASE_CONFIG', 'database');
define('DEFAULT_DATABASE_CONFIG', 'main');
define('DEFAULT_DATABASE_ENGINE', 'INNODB');
define('CORE_CONFIG', 'core');

// Dirs
define('FRAME_PATH_CONFIG', APP_ROOT.'frame/');
define('RUNTIME_PATH_CONFIG', APP_ROOT.'../runtime/');

// Database Engine And QQ
define('CDb', \Core\Parts\Database\Engines\CDbSimpleMysql::class);
define('CDbQueryConstructor', \Core\Parts\Database\Query_Constructors\CDbSimpleQueryConstructor::class);
