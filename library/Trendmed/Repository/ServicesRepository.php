<?php
namespace Trendmed\Repository;
/**
 * Description of ReservationsRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ServicesRepository extends \Doctrine\ORM\EntityRepository
{

    public function fetchLatestServices($amount = 5)
    {
        $dql = 'SELECT c FROM \Trendmed\Entity\Service c WHERE c.isActive = ?1 ORDER BY c.created DESC';
        $query = $this->_em->createQuery($dql);
        $query->setParameter(1, true);
        $query->setMaxResults($amount);

        return $query->getResult();
    }
}