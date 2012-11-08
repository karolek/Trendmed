<?php
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Trendmed\Fixtures;

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

// Bootstrapping resources
$bootstrap = $application->bootstrap()->getBootstrap();
print "Bootstraping the application for ENV: ".APPLICATION_ENV."\n";

print "Retrieve Doctrine resources\n";
// Retrieve Doctrine Container resource
$container = $bootstrap->getResource('doctrine');
$em = $container->getEntityManager();

print "Loading fixtures\n";
// adding fixtures
$loader = new Loader();
$fixturesDir = APPLICATION_PATH .'/../library/Trendmed/Fixtures/';
$loader->loadFromDirectory($fixturesDir);
$c = count($loader->getFixtures());
print "$c fixtures loaded from $fixturesDir\n";

// executing fixtures
$purger = new ORMPurger();
$executor = new ORMExecutor($em, $purger);
$executor->execute($loader->getFixtures());

print "Fixtures load into DB\n";

// creating public directories
if (!file_exists(APPLICATION_PATH . '/../public/clinicPhotos')) {
    mkdir(APPLICATION_PATH . '/../public/clinicPhotos');
    print "created directory public/clinicPhotos\n";
}
if (!file_exists(APPLICATION_PATH . '/../public/clinicPhotos/originals')) {
    mkdir(APPLICATION_PATH . '/../public/clinicPhotos/originals');
    print "created directory public/clinicPhotos/originals\n";
}
print "END\n";
