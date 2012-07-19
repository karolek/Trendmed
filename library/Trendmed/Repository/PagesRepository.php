<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class PagesRepository extends \Doctrine\ORM\EntityRepository {

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

        switch($limitToType) {
            case false:
                $qb->where('p.type = "aritcle_sponsored"')
                    ->andWhere('p.type = "article_normal"');
                break;
            case 'sponsored':
                //TODO
                break;
            case 'normal':
                //todo
                break;
        }
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result[0];
    }
}