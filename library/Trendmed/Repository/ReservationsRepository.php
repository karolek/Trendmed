<?php
namespace Trendmed\Repository;
/**
 * Description of ReservationsRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ReservationsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param \Trendmed\Entity\Patient $patient
     * @return mixed
     */
    public function fetchAllPatientReservations(\Trendmed\Entity\Patient $patient)
    {
        $dql = "SELECT r FROM \Trendmed\Entity\Reservation r WHERE r.patient = :patient_id";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('patient_id', $patient->id);
        $reservations = $query->getResult();
        return $reservations;
    }

    /**
     * @param \Trendmed\Entity\Clinic    $patient
     * @return mixed
     */
    public function fetchAllClinicReservations(\Trendmed\Entity\Clinic $clinic)
    {
        $dql = "SELECT r FROM \Trendmed\Entity\Reservation r WHERE r.clinic = :clinic_id";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('clinic_id', $clinic->id);
        $reservations = $query->getResult();
        return $reservations;
    }
}