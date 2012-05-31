<?php
namespace Trendmed\Repository;
/**
 * Description of ClinicRepository
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class CategoryRepository extends \Gedmo\Tree\Entity\Repository\NestedTreeRepository {
    
    public function findAllAsArray($rootId = 1, $recursive = true)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.root = 1')
            ->andWhere('node.lvl = 1')
            ->getQuery();

        //$options = array('decorate' => true);
        $tree = $query->getArrayResult();
    }

    public function findOneAsArray($id)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->where('node.id = ?1')
            ->getQuery();
        $query->setParameter(1, $id);
        $tree = $query->getArrayResult();
        return $tree[0]; //return only first element
    }
}