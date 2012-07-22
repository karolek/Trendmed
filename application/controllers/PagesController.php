<?php
use Doctrine\ORM\Tools\Pagination\Paginator;

class PagesController extends Zend_Controller_Action
{

    protected $_em;
    protected $_repo;


    public function init()
    {
        /* Initialize action controller here */
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Page');
    }

    public function viewAction()
    {
        $request = $this->getRequest();
        $slug = $request->getParam('slug');
        $type = $request->getParam('type', 'Text page');
        $page = $this->_repo->findOneBySlug($slug);
        if (!$page) {
            throw new \Exception('Page not found', 404);
        }
        if (!$page->isActive()) {
            throw new \Exception('this page is not active', 404);
        }
        $this->view->page = $page;
        $this->view->headTitle($page->title);

        // TODO: change layout based on type of a page
    }

    /**
     * Lista artykułów z wyszukiwarką i paginowaną listą
     */
    public function archiveAction()
    {
        // pobieranie
        $this->view->headTitle($this->view->translate('Articles list'));
        $this->_helper->_layout->setLayout('homepage');
        $config = \Zend_Registry::get('config');

        $request = $this->getRequest();
        $page = $request->getParam('page', 1);
        $order = $request->getParam('order', 'created');
        $direction = $request->getParam('direction', 'DESC');

        // fetching data to paginator
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('\Trendmed\Entity\Page', 'p')
            ->orderBy('p.' . $order, $direction)
            ->where('p.isActive = ?1')
            ->setFirstResult(($config->pagination->pages->archiwum * $page) - $config->pagination->pages->archiwum)
            ->setMaxResults($config->pagination->pages->archiwum);

        $qb->setParameter(1, 1); // only active


        $paginator = new Paginator($qb, $fetchJoin = true);

        $c = count($paginator);
        $i = $config->pagination->pages->archiwum; // items per page
        $numOfPages = $c / $i;
        // Making of a Zend_Paginator
        $zendPaginator = \Zend_Paginator::factory($c);
        $zendPaginator->setCurrentPageNumber($page);
        $zendPaginator->setItemCountPerPage($config->pagination->pages->archiwum);
        $this->view->zendPaginator = $zendPaginator;
        $this->view->pageCount = $numOfPages;
        $this->view->articles = $paginator;
        // adding script path to find pagination file
        $this->view->addScriptPath(APPLICATION_PATH . '/layouts/scripts/');

    }
}

