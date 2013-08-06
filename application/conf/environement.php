<?php

//
define('APPLICATION_PATH', realpath(__DIR__ . '/../'));

define('CONF_PATH', realpath(APPLICATION_PATH . '/conf'));
define('LIBRARY_PATH', realpath(APPLICATION_PATH . '/../library'));
define('WEB_PATH', realpath(APPLICATION_PATH . '/../public'));
define('TMP_PATH', realpath(APPLICATION_PATH . '/../tmp'));

//var_dump(APPLICATION_PATH);
//var_dump(LIBRARY_PATH);
//var_dump(WEB_PATH);
//var_dump(TMP_PATH);


// Mise en place de l'autoloader.
require_once LIBRARY_PATH . '/SplClassLoader.php';
$classLoader = new SplClassLoader();
$classLoader->setIncludePath(APPLICATION_PATH . '/server');
$classLoader->setFileExtension('.class.php');
$classLoader->register();

// Initialisation de l'IOC.
Ioc\Manager::initClientContainerInstance();
Ioc\Manager::initServerContainerInstance();