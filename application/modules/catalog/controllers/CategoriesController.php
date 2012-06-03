<?php
/**
 * Controller takes care of displaying the clinics in categories
 * with pagination and sorting
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
use Doctrine\ORM\Tools\Pagination\Paginator;

class Catalog_CategoriesController extends \Zend_Controller_Action
{

    protected $_em; //entity manager
    protected $_repo; // categories repository, for use of use

    public function init()
    {
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Category');
    }

    public function viewAction()
    {
        $category = $this->_fetchCategoryFromParams();
        $this->view->category = $category;
        $this->view->headTitle($category->name);
        $this->view->selectedCategory = $category->slug;
        $this->_helper->_layout->setLayout('homepage');
        $config = \Zend_Registry::get('config');

        $request    = $this->getRequest();
        $page       = $request->getParam('page', 1);
        $order      = $request->getParam('order', 'created');
        $direction  = $request->getParam('direction', 'DESC');

        // fetching data to paginator
        $qb = $this->_em->createQueryBuilder()
            ->select('s')
            ->from('\Trendmed\Entity\Service', 's')
            ->join('s.clinic', 'c')
            ->orderBy('s.'.$order, $direction)
            ->where('s.category = ?1')
            ->setFirstResult(($config->pagination->catalog->clinics * $page) - $config->pagination->catalog->clinics)
            ->setMaxResults($config->pagination->catalog->clinics);
        $qb->setParameter(1, $category->id);

        $paginator = new Paginator($qb, $fetchJoin = true);

        $c = count($paginator);
        $i = $config->pagination->catalog->clinics; // items per page
        $numOfPages = $c / $i;
        // Making of a Zend_Paginator
        $zendPaginator = \Zend_Paginator::factory($c);
        $zendPaginator->setCurrentPageNumber($page);
        $zendPaginator->setItemCountPerPage($config->pagination->homePage->projects);
        $this->view->zendPaginator = $zendPaginator;
        $this->view->numOfPages = $numOfPages;
        $this->view->services = $paginator;

    }

    protected function _fetchCategoryFromParams()
    {
        $request = $this->getRequest();
        $slug = $request->getParam('slug');
        if(!$slug) throw new \Exception('No param given in '.__FUNCTION__);
        // fetching the category
        $category = $this->_repo->findOneBySlug($slug);
        if(!$category) throw new \Exception('No category with slug: '.$slug, 404);
        return $category;
    }

}

