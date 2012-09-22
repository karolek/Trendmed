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
        $dql = "SELECT r FROM \Trendmed\Entity\Reservation r WHERE r.patient = :patient_id ORDER BY r.created DESC";
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
        $dql = "SELECT r FROM \Trendmed\Entity\Reservation r WHERE r.clinic = :clinic_id ORDER BY r.created DESC";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('clinic_id', $clinic->id);
        $reservations = $query->getResult();
        return $reservations;
    }

    /**
     *  if reservation is confirmed, and requires payment and not paid and now is before from date
     *
     */
    public function findAllUnpaidAndDue()
    {
        $dql = "
            SELECT r FROM \Trendmed\Entity\Reservation r
            JOIN r.patient p
            WHERE r.status = :status
            AND r.billStatus = :billStatus
            AND r.dateFrom < :dateFrom";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('status', 'confirmed');
        $query->setParameter('billStatus', \Trendmed\Entity\Reservation::BILL_STATUS_NOT_PAID);
        $query->setParameter('dateFrom', new \DateTime());

        return $reservations = $query->getResult();
    }

    public function findAllPaidAndDue()
    {
        $dql = "
            SELECT r FROM \Trendmed\Entity\Reservation r
            JOIN r.patient p
            WHERE r.status = :status
            AND r.billStatus = :billStatus
            AND r.dateFrom < :dateFrom";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('status', 'confirmed');
        $query->setParameter('billStatus', \Trendmed\Entity\Reservation::BILL_STATUS_PAID);
        $query->setParameter('dateFrom', new \DateTime());

        return $reservations = $query->getResult();
    }

    public function findAllDueForSurvey($maxNotifications = 3, $timeIntervalH = 72)
    {
        $interval = new \DateInterval("PT".$timeIntervalH."H");
        $now = new \DateTime();
        $lastSendMax = $now->sub($interval);

        $dql = "
            SELECT r FROM \Trendmed\Entity\Reservation r
            WHERE NOT EXISTS (SELECT s FROM \Trendmed\Entity\Rating s WHERE s.reservation = r.id)
            AND r.status = :status
            AND r.billStatus = :billStatus
            AND r.dateTo < :dateTo
            AND r.amountOfReminderAboutSurveySend = :maxNotifications
            AND r.lastReminderAboutSurveySend < :lastSendMax";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('status', 'confirmed');
        $query->setParameter('billStatus', \Trendmed\Entity\Reservation::BILL_STATUS_PAID);
        $query->setParameter('dateTo', new \DateTime());
        $query->setParameter('maxNotifications', $maxNotifications);
        $query->setParameter('lastSendMax', $lastSendMax);

        return $reservations = $query->getResult();
    }

}