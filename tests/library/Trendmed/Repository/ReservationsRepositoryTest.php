<?php
namespace Trendmed\Entity;
use \Doctrine\Common\Util\Debug as Debug;
use \Trendmed\Entity\Reservation;
/**
 * Description of CategoryTestCase
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ReservationRepositoryTest extends \ModelTestCase {
	
    public function testFindingReservationForSurvery()
    {
        $repo = $this->em->getRepository('\Trendmed\Entity\Reservation');


    }

    public function makeReservation()
    {

        # make clinic
        $clinic = new \Trendmed\Entity\Clinic;
        $clinic->name = 'TrendMed2';
        $clinic->streetaddress = 'Topolowa 2/7';
        $clinic->province = 'Pomorskie';
        $clinic->city = 'Gdańsk';
        $clinic->postcode = '80-233';
        $clinic->repPhone = '+48 512 129 709';
        $clinic->repName = 'Bartosz';
        $clinic->repEmail = 'b@br-design.pl';
        $clinic->type = 'Clinic';
        $clinic->password = 'nataniel';
        $clinic->salt = $clinic->generateSalt();
        $clinic->geoLat = 54.377608;
        $clinic->getLon = 18.595605;

        $role = $this->em->getRepository('\Trendmed\Entity\Role')
            ->findOneByName($clinic->getRoleName());

        if(!$role) throw new Exception('Given role ('.$clinic->getRoleName().'
            not found in DB');
        $clinic->setRole($role);
        $this->em->persist($clinic);

        # patient
        $patient = new \Trendmed\Entity\Patient();
        $patient->name = 'Bartosz Rychlicki';
        $patient->login = 'bartosz.rychlicki@gmail.com';
        $patient->password = 'haslo123';

        $role = $this->em->getRepository('\Trendmed\Entity\Role')
            ->findOneByName($patient->getRoleName());
        $patient->setRole($role);

        $this->em->persist($patient);

        $reservation = new Reservation();
        $reservation->setPatient($patient);
        $reservation->setClinic($clinic);
    }

}