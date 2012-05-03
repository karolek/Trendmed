<?php
// Here you can initialize variables that will for your tests
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
\Zend_Loader_Autoloader::getInstance();
// Create application, bootstrap, and run
$application = new Zend_Application(
   APPLICATION_ENV,
   APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$em = \Zend_Registry::get('doctrine')->getEntityManager();
\Codeception\Module\Doctrine2::$em = $em;
