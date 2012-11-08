<?php
$benchmarkStart = microtime(true);
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
// fetch environment from files
$env = trim(file_get_contents(APPLICATION_PATH . '/../.env'));
if(!$env)
    $env = getenv('APPLICATION_ENV');
if(!$env)
    throw new \Exception('No information about environment stage available. Add .env file to Your root
        directory or add APPLICATION_ENV to apache configuration');
define('APPLICATION_ENV', $env);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));
## Autoloader from Composer ##
require_once __DIR__.'/../vendor/autoload.php';

/** Merging configs to use in application Bootstrap */
$appConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',
    APPLICATION_ENV, TRUE);
$dbConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini',
    APPLICATION_ENV);
$appConfig->merge($dbConfig);
Zend_Registry::set('config', $appConfig);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, $appConfig
);
$application->bootstrap()
            ->run();

// counting exection time
// remove after some time
/*
$bechmarkEnd = microtime(true);
$generateTime = ($bechmarkEnd)-($benchmarkStart);
$log = \Zend_Registry::get('log');
if ($generateTime > 5) {
    $log->warn(sprintf( $_SERVER['REQUEST_URI']. ' DŁUGI CZAS, wczytywanie trwało in %.3fs', $generateTime));
} else {
    $log->info(sprintf( $_SERVER['REQUEST_URI']. ' wczytywanie trwało in %.3fs', $generateTime));
}
*/
