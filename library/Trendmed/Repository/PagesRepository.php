<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class PagesRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param int $limit
     * @param string $limitToType string sponsored|normal Pozwala na pobranie tylko sponsorowanych,
     * lub tylko normalnych artów
     * @return mixed
     */
    public function fetchLatestArticles($limit = 5, $limitToType = false)
    {
        $qb = new \Doctrine\ORM\QueryBuilder($this->_em);
        $qb->select('p')
            ->from('\Trendmed\Entity\Page', 'p');

        switch ($limitToType) {
            case false:
                $qb->where('p.type = ?1')
                    ->orWhere('p.type = ?2');
                break;
            case 'sponsored':
                $qb->where('p.type = ?1');
                break;
            case 'normal':
                $qb->where('p.type = ?2');
                break;
        }
        $qb->andWhere('p.isActive = ?3'); // for active
        $qb->setParameters(array(
            '1' => 'aritcle_sponsored',
            '2' => 'article_normal',
            '3' => 1
        ));
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}