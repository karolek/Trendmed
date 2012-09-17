<?php
namespace Trendmed\Repository;
/**
 * Description
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class AdsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOneToShowNextForZone($zone)
    {
        $entity = $this->_runSelectForZoneBanners($zone);
        // if no object found then we need to reset all shown values to false and select again
        if (!$entity) {
            $this->_resetTurnForZone($zone);
            $entity = $this->_runSelectForZoneBanners($zone);
        }

        // if still no entity then we have no banners for this zone \
        if (!$entity) {
            return false;
        }
        return $entity;
    }

    protected function _resetTurnForZone($zone)
    {
        $qb = $this->_em->createQueryBuilder()
            ->update('\Trendmed\Entity\BannerAd', 'a')
            ->set('a.shown', '0')
            ->where('a.zone = ?1');
        $qb->setParameter(1, $zone);
        $query = $qb->getQuery();
        return $query->execute();
    }

    protected function _runSelectForZoneBanners($zone)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from('\Trendmed\Entity\BannerAd', 'a')
            ->where('a.isActive = ?1')
            ->orderBy('a.created')
            ->andWhere('a.zone = ?2')
            ->andWhere('a.shown = ?3');
        $qb->setParameter(1, true);
        $qb->setParameter(2, $zone);
        $qb->setParameter(3, false);
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }
}