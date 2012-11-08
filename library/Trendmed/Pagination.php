<?php
namespace Trendmed;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * This class handles the pagination.
 * Uses doctrine Paginator and Zend Paginator for view stuff.
 *
 * Mini-tutorial
 * <code>
 * $qb = $this->_em->createQueryBuilder()
 * ->select('s')
 * ->from('\Trendmed\Entity\Service', 's')
 * ->join('s.clinic', 'c')
 * ->orderBy('s.' . $order, $direction)
 * ->where('s.category = ?1');
 * $qb->setParameter(1, $category->id);
 *
 * $pagination = new \Trendmed\Pagination($qb->getQuery(), $config->pagination->catalog->clinics, $page);
 *
 * $this->view->zendPaginator = $pagination->getZendPaginator();
 * $this->view->numOfPages = $pagination->getPagesCount();
 * $this->view->services = $pagination->getItems();
 * </code>
 *
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 * @date 28.07.2012
 */
class Pagination
{

    /**
     * @var \Doctrine\ORM\Tools\Pagination\Paginator
     */
    protected $paginator;
    /**
     * @var int numbers of items per page
     */
    protected $itemsPerPage;
    /**
     * @var int page that user is fetching
     */
    protected $currentPage;

    /**
     * @var int total number of items in persistance layer
     */
    protected $itemsCount;

    /**
     * @var int total number of pages with items in persistence layer
     */
    protected $pagesCount;

    public function __construct(\Doctrine\Orm\Query $query, $itemsPerPage, $currentPage = 1)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;

        // first make offset for query
        $query
            ->setFirstResult(($this->itemsPerPage * $this->currentPage) - $this->itemsPerPage)
            ->setMaxResults($this->itemsPerPage);

        // constructing doctrine paginator
        $this->paginator = new Paginator($query, $fetchJoin = true);
    }

    public function getItemsCount()
    {
        if (!$this->itemsCount) {
            $this->itemsCount = count($this->paginator);
        }
        return $this->itemsCount;
    }

    public function getPagesCount()
    {

        if (!$this->pagesCount) {
            $this->pagesCount = $this->getItemsCount() / $this->itemsPerPage;
        }
        return $this->pagesCount;
    }

    /**
     * @return \Zend_Paginator
     */
    public function getZendPaginator()
    {
        $zendPaginator = \Zend_Paginator::factory($this->getItemsCount());
        $zendPaginator->setCurrentPageNumber($this->currentPage);
        $zendPaginator->setItemCountPerPage($this->itemsPerPage);
        return $zendPaginator;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getItems()
    {
        return $this->paginator;
    }
}
