<?php

class IndexController extends \Zend_Controller_Action
{

    protected $_em;
    
    public function init()
    {
        $this->_em = $this->_helper->getEm();
        /* Initialize action controller here */
    }

    public function adRedirectAction()
    {
        $req = $this->getRequest();
        $adId = $req->getParam('id', null);
        // I will use adId as filename to hash the id in URL
        $repo = $this->_em->getRepository('\Trendmed\Entity\BannerAd');
        $ad = $repo->findOneByFile($adId);
        if(!$ad) throw new \Exception('Cant find ad with filename :'.$adId);

        $ad->clickCount++;
        $this->_em->persist($ad);
        $this->_em->flush();
        $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $r->setCode(301);
        $r->gotoUrl($ad->target)->redirectAndExit();
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
            ->fetchLatestClinics(3);

        # fetching popular services for home page
        $this->view->newServices = $this->_em->getRepository('\Trendmed\Entity\Service')
            ->fetchLatestServices(3);

        # fetching popular clinics
        $this->view->popularClinics = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findMostPopular(3);
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

    /**
     * Used by AJAX request to fetch sub categories for add new service.
     * Subcategories will be filtered by categories allready used by clinic
     */
    public function getSubcategoriesForClinicAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $parentId = $request->getParam('parentId');
            $repo = $this->_em->getRepository('\Trendmed\Entity\Category');
            $subcategories = $repo->findForParentAsArray($parentId, $this->_helper->LoggedUser()->usedCategories());
            $json = \Zend_Json::encode($subcategories);
            echo $json;
        } else {
            throw new \Exception('Invalid request type in '.__FUNCTION__);
        }
    }


}

