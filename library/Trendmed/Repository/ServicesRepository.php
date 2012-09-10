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
        $dql = 'SELECT c FROM \Trendmed\Entity\Service c ORDER BY c.created DESC';
        $query = $this->_em->createQuery($dql);
        $query->setMaxResults($amount);

        return $query->getResult();
    }
}