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
        $search = $request->getParam('article_search', null);

        // fetching data to paginator
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('\Trendmed\Entity\Page', 'p')
            ->where('p.isActive = ?1')
            ->andWhere('p.type IN (?2)')
            ->orderBy('p.' . $order, $direction);

        $qb->setParameter(1, 1); // only active
        $qb->setParameter(2, array('article_normal', 'article_sponsored'));

        // search for article
        if ($search) {
            $qb->andWhere('p.title LIKE ?3');
            $qb->setParameter(3, '%' . $search . '%');
            $this->view->article_search = $search;
        }

        $pagination = new \Trendmed\Pagination($qb->getQuery(), $config->pagination->pages->archiwum, $page);

        $this->view->zendPaginator = $pagination->getZendPaginator();
        $this->view->pageCount = $pagination->getPagesCount();
        $this->view->articles = $pagination->getItems();

        // adding script path to find pagination file
        $this->view->addScriptPath(APPLICATION_PATH . '/layouts/scripts/');

    }

}

