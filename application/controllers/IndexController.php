<?php

class IndexController extends \Zend_Controller_Action
{

    protected $_em;
    
    public function init()
    {
        $this->_em = $this->_helper->getEm();
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->view->headTitle('Homepage');
        $this->_helper->layout()->setLayout('homepage');

        // fetching latest articles for home page
        $this->view->articles = $this->_em->getRepository('\Trendmed\Entity\Page')
            ->fetchLatestArticles(3);

        # fetching latest clinics for home page
        $this->view->newClinics = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->fetchLatestClinics(5);
    }

    /**
     * Return some information about request category
     */
    public function getCategoriesAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $parentId = $request->getParam('parentId');
            $repo = $this->_em->getRepository('\Trendmed\Entity\Category');
            $subcategories = $repo->findForParentAsArray($parentId);
            $json = \Zend_Json::encode($subcategories);
            echo $json;
        } else {
            throw new \Exception('Invalid request type in '.__FUNCTION__);
        }
    }


}

