<?php
namespace Trendmed\Repository;
/**
 * This is very importand repository. It fetches and orders the main tree of categories.
 * It serves to display a front-end main menu, back end management and services management (categories in select)
 * It operates on arrays
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class CategoryRepository extends \Gedmo\Tree\Entity\Repository\NestedTreeRepository {

    /**
     * @param int $rootId
     * @param bool $recursive
     * @deprecated use findAllAsTree
     */
    public function findAllAsArray($rootId = 1, $recursive = true)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            //->orderBy('node.name, node.lft', 'ASC')
            ->where('node.root = 1')
            ->andWhere('node.lvl = 1')
            ->getQuery();

        //$options = array('decorate' => true);
        $tree = $query->getArrayResult();
    }

    /**
     * @param $id
     * @return mixed
     * @deprecated
     */
    public function findOneAsArray($id)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->where('node.id = ?1')
            ->getQuery();
        $query->setParameter(1, $id);
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );


        $tree = $query->getArrayResult();

        return $tree[0]; //return only first element
    }

    /**
     * This method fetches categories for one given parent. Used for example in select where You want
     * to fetch only categories for given main category and not the whole tree.
     * You can exclude some categories from result by passing a collection of services (used if you want to get
     * categories not used by clinic yet)
     *
     * @param int $parentId
     * @return array flat array of elements for a given parrent
     */
    public function findForParentAsArray($parentId = 1, \Doctrine\Common\Collections\Collection $excludeCategories = null)
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->orderBy('node.name, node.lft', 'ASC')
            ->where('node.parent = ?1');
        if (count($excludeCategories) > 0) {
            $ids = array();
            foreach($excludeCategories as $category) {
                $ids[] = $category->id;
            }
            $qb->andWhere($qb->expr()->notIn('node.id', join(',', $ids)));
        }
        $query = $qb->getQuery();
        #echo $query->getDQL(); exit();
        $query->setParameter(1, $parentId);
        //$options = array('decorate' => true);
        $tree = $query->getArrayResult();
        return $tree;
    }

    /**
     * This method serves as a proxy method. You could just use buildTree when You need the categories tree,
     * but thanks to one method You can for example change order of elements in once place
     *
     * @param array $options
     * @return array|string
     * @throws \Exception
     */
    public function findAllAsArrayTree($options = array())
    {
        // we must check if there is a root in categories
        $root = $this->findOneByLvl(0);
        if(!$root) {
            throw new \Exception('No root in categories');
        }
        $tree = $this->childrenHierarchy($root, false, $options);

        return $tree;
    }

    public function findAllMainCategoriesAsArray()
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->orderBy('node.name, node.lft', 'ASC')
            ->where('node.lvl = 1')
            //->andWhere('node.lvl = 1')
            ->getQuery();

        //$options = array('decorate' => true);
        $tree = $query->getArrayResult();
        return $tree;
    }

    public function getRootNode()
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('node')
            ->from('Trendmed\Entity\Category', 'node')
            ->where('node.name = ?1')
            ->andWhere('node.lvl = 0')
            ->getQuery();
        $query->setParameter(1, 'root');
        return $query->getResult()[0];

    }
}