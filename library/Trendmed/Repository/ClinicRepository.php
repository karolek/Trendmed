<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicRepository extends \Doctrine\ORM\EntityRepository
{

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

    public function findByNameOrCity($name, $city)
    {
        $qb = new \Doctrine\ORM\QueryBuilder($this->_em);
        $qb->select('c')
            ->from('\Trendmed\Entity\Clinic', 'c')
            ->where('c.name LIKE ?1')
            ->orWhere('c.city LIKE ?2');
        $qb->setParameter(1, '%' . $name . '%');
        $qb->setParameter(2, '%' . $city . '%');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Fetches all distinct city names from all active clinics
     */
    public function findDistinctClinicCitiesAsArray()
    {
        $dql = 'SELECT DISTINCT c.city FROM \Trendmed\Entity\Clinic c';
        $query = $this->_em->createQuery($dql);
        return $query->getArrayResult();
    }
}