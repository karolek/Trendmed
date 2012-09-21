<?php

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
$logger = $bootstrap->getResource('log');

## checking payments do
    # if reservation is confirmed, and requires payment and not paid and now is before from date
    $unpaidReservations = $em->getRepository('\Trendmed\Entity\Reservation')
        ->findAllUnpaidAndDue();
    print count($unpaidReservations).' unpaid reservations found, checking if I will be sending reminders'."\n";
    # how much time must past before sending another reminder
    $interval = $appConfig->reservations->reservations->reminderAboutPaymentInterval; # in hours
    $i = 0;
    $j = 0;
    foreach ($unpaidReservations as $reservation) {
        # checking if the last send email was not to soon and if limit of e-mails send is not meet

        if($reservation->amountOfReminderAboutPaymentSend < new \DateTime()
            AND
                $reservation->amountOfReminderAboutPaymentSend < $appConfig->reservations->reminderAboutPaymentLimit
            AND
                $reservation->getLastReminderAboutPaymentSend()->diff(new \DateTime())->days * 24 < $interval ) {
            $reservation->sendStatusNotification('paymentReminder');
            $i++;
        }
        $j++;
    }

    $log = 'Reminder about payment send to '. $i .' patients of '. $j .' total reservations'."\n";
    print $log;
    $logger->debug($log);


    print "END\n";
