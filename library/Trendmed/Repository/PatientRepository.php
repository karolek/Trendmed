<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class PatientRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Filter and fetches data to use in CSV for import in newsletter
     * @return array
     */
    public function findForNewsletter()
    {
        $dql = 'SELECT p.login, p.name FROM \Trendmed\Entity\Patient p WHERE p.isNewsletterActive = :param1 ORDER BY p.created';
        $query = $this->_em->createQuery($dql);
        $query->setParameter('param1', true);
        return $query->getArrayResult();
    }
}