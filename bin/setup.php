<?php
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Trendmed\Fixtures;

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Creating application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

print "Bootstraping the application for ENV: ".APPLICATION_ENV."\n";
// Bootstrapping resources
$bootstrap = $application->bootstrap()->getBootstrap();

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
print "END\n";
