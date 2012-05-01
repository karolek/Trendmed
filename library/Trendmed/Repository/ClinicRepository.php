<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicRepository extends \Doctrine\ORM\EntityRepository {
    
    public function findOneAsArray($id)
    {
        $qb = new \Doctrine\ORM\QueryBuilder($this->_em);
        $qb->select('c')
                ->from('\Trendmed\Entity\Clinic', 'c')
                ->where('c.id = ?1');
        $qb->setParameter(1, $id);
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result[0];
    }
}