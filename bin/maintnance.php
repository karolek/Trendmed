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
require_once __DIR__ . '/../vendor/autoload.php';

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
print "Bootstraping the application for ENV: " . APPLICATION_ENV . "\n";

print "Retrieve Doctrine resources\n";
// Retrieve Doctrine Container resource
$container = $bootstrap->getResource('doctrine');
$em = $container->getEntityManager();
$logger = $bootstrap->getResource('log');
$interval = $appConfig->reservation->reminderAboutPaymentInterval; # in hours
if (!is_numeric($interval)) {
    throw new \Exception ('No interval for reminders defined in config->reservation->reminderAboutPaymentInterval');
}

## checking payments do
print "Checking reservation for due and unpaid to send payment reminders...\n";
# if reservation is confirmed, and requires payment and not paid and now is before from date
$unpaidReservations = $em->getRepository('\Trendmed\Entity\Reservation')
    ->findAllUnpaidAndDue();
print count($unpaidReservations) . ' unpaid reservations found, checking if I will be sending reminders' . "\n";
# how much time must past before sending another reminder
$i = 0;
$j = 0;
foreach ($unpaidReservations as $reservation) {
    $j++;
    # checking if the last send email was not to soon and if limit of e-mails send is not meet
    if ($reservation->amountOfReminderAboutPaymentSend >= $appConfig->reservation->reminderAboutPaymentLimit) {
        print ('wysłano: ' . $reservation->amountOfReminderAboutPaymentSend . ' z limitu ' . $appConfig->reservation->reminderAboutPaymentLimit . "\n");
        continue;
    }

    if ($reservation->getLastReminderAboutPaymentSend()->diff(new \DateTime())->days * 24 < $interval) {
        print ('czas do nastepnej wysyłki jeszcze nie minął' . "\n");
        print ("Interval is (in hours) $interval\n");
        print ("Last reminder send: " . $reservation->getLastReminderAboutPaymentSend()->diff(new \DateTime())->days * 24) . " hours ago\n";
        continue;
    }
    $reservation->sendStatusNotification('paymentReminder');
    # save date of this reminder
    $reservation->amountOfReminderAboutPaymentSend =+ 1;
    $reservation->setLastReminderAboutPaymentSend(new \DateTime('now'));
    $em->persist($reservation);
    $i++;
}

$log = 'Reminder about payment send to ' . $i . ' patients of ' . $j . ' total reservations' . "\n";
print $log;
$logger->debug($log);

## making a reminder about reservation
print "Sending reminders about reservation to patients...\n";
$confirmedAndPaid = $em->getRepository('\Trendmed\Entity\Reservation')
    ->findAllPaidAndDue();
print count($confirmedAndPaid) . ' paid and due reservations found in the system' . "\n";
$i = 0;
$j = 0;
foreach ($confirmedAndPaid as $reservation) {
    $j++;
    # checking if the last send email was not to soon and if limit of e-mails send is not meet
    if ($reservation->amountOfReminderAboutReservationSend >= $appConfig->reservation->reminderAboutReservationLimit) {
        print ('wysłano: ' . $reservation->amountOfReminderAboutReservationSend . ' z limitu ' . $appConfig->reservation->reminderAboutReservationLimit . "\n");
        continue;
    }

    if ($reservation->getLastReminderAboutReservationSend()->diff(new \DateTime())->days * 24 < $interval) {
        print ('czas do nastepnej wysyłki jeszcze nie minął' . "\n");
        print ("Interval is (in hours) $interval\n");
        print ("Last reminder send: " . $reservation->getLastReminderAboutPaymentSend()->diff(new \DateTime())->days * 24) . " hours ago\n";
        continue;
    }
    $reservation->sendStatusNotification('reservationReminder');
    # save date of this reminder
    $reservation->amountOfReminderAboutReservationSend =+ 1;
    $reservation->setLastReminderAboutReservationSend(new \DateTime('now'));
    $em->persist($reservation);

    $i++;
}


## making a reminder about survey
## must be after "dateTo" todo: later change this to use new reserrvation accurate date system
print "Checking reservations for all that are after dateTo and are confirmed and dont have survery...\n";
$surveyDue = $em->getRepository('\Trendmed\Entity\Reservation')
    ->findAllDueForSurvey($appConfig->reservation->reminderAboutSurveryLimit, $appConfig->reservation->reminderAboutSurveyInterval);
print "\t".count($surveyDue)." reservations found\n";
foreach ($surveyDue as $reservation) {
    $reservation->sendStatusNotification('surveyReminder');
    # save date of this reminder
    $reservation->amountOfReminderAboutSurveySend =+ 1;
    $reservation->setLastReminderAboutSurveySend(new \DateTime('now'));
    $em->persist($reservation);
}
print "Flushing to database\n";
$em->flush();
print "Flushed\n";
print "END\n";
