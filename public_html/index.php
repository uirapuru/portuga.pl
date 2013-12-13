<?php

error_reporting(1);

date_default_timezone_set("Europe/Paris");
ini_set('zend.ze1_compatibility_mode', 0);

defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define('BASE_PATH', str_replace('\\', '/', realpath(dirname(__file__))));

//set_include_path(implode(PATH_SEPARATOR, array(
//            realpath(APPLICATION_PATH . '/../library'),
//            realpath(APPLICATION_PATH . '/../../library'),
//            get_include_path(),
//        )));

require_once 'Zend/Application.php';
require_once 'Zend/Loader/Autoloader.php';

$oAutoloader = Zend_Loader_Autoloader::getInstance();
$oAutoloader->registerNamespace('Core_');

$oApplication = new Zend_Application(
                APPLICATION_ENV,
                array(
                    'pluginPaths' => array(
//                        'ZendX_Application_Resource' => 'ZendX/Application/Resource'
                    ),
                    'config' => APPLICATION_PATH . '/configs/application.ini'
                )
);

// odpalamy Zenda ..

try
{
    $oApplication->bootstrap()->run();
}
catch (Exception $e)
{
    echo "<pre>";
    echo $e->getMessage();
    print_r($e->getTraceAsString());

}
