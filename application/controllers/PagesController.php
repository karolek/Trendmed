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
        if(!$page) {
            throw new \Exception('Page not found', 404);
        }
        if($page->type != $type) {
            throw new \Exception('this is wrong page type');
        }
        if (!$page->isActive()) {
            throw new \Exception('this page is not active', 404);
        }
        $this->view->page = $page;
        $this->view->headTitle($page->title);

        // TODO: change layout based on type of a page
    }
}

