<?php
/** This is controller of text pages handle */
use Doctrine\ORM\Tools\Pagination\Paginator;

class Admin_PagesController extends \Zend_Controller_Action
{
    protected $_em; // doctrine entity manager
    protected $_repo; // pages repository
    
    public function init()
    {
        $this->_em = $this->getDoctrineContainer()->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Page');
    }
    
    /**
     * Retrieve the Doctrine Container.
     *
     * @return Bisna\Application\Container\DoctrineContainer
     */
    public function getDoctrineContainer() {
        return $this->getInvokeArg('bootstrap')->getResource('doctrine');
    }

    /**
     * List all subpages
     */
    public function indexAction()
    {
        $this->view->headTitle('Strony tekstowe');
        $pages = $this->_repo->findAll();
        $this->view->pages = $pages;
    }

    public function savePageAction()
    {
        $req = $this->getRequest();
        $pageId = $req->getParam('id', null);

        $form = new Admin_Form_Page();

        if($pageId) {
            $entity = $this->_repo->find($pageId);
            $form->setDefault('id', $pageId);
            $form->populate(array(
                'title' => $entity->getTitle(),
                'content' => $entity->getContent(),
                'type'      => $entity->getType(),
            ));
            $this->view->headTitle('Edit page');
        } else {
            $this->view->headTitle('Add new page');
            $entity = new \Trendmed\Entity\Page;
        }

        if($req->isPost()) {
            $post = $req->getPost();
            if($form->isValid($post)) {
                $values = $form->getValues();
                $entity->setOptions($values);
                $this->_em->persist($entity);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Page has been saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Please, correct any errors in the form'));
            }
        }

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view, array('content'), 'AdminToolbar');
    }

    public function deletePageAction()
    {
        $request = $this->getRequest();

        $this->view->headTitle('Delete page');
        if($request->getParam('id')) {
            $entityId   = $request->getParam('id');
            $entity     = $this->_repo->find($entityId);
            if(!$entity) throw new \Exception('No entity found');
            $this->_em->remove($entity);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array(
                'warning' => 'Page has been deleted'
                ));
            $this->_helper->Redirector('index', 'pages', 'admin');
        } else {
            throw new \Exception('ID to delete not given');
        }
    }

    public function activateAction()
    {
        $request = $this->getRequest();

        $this->view->headTitle('Delete page');
        if($request->getParam('id')) {
            $entityId   = $request->getParam('id');
            $entity     = $this->_repo->find($entityId);
            if(!$entity) throw new \Exception('No entity found');

            $entity->setActive(!$entity->isActive());
            $this->_em->persist($entity);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array(
                'warning' => 'Active state changed'
            ));
            $this->_helper->Redirector('index', 'pages', 'admin');
        } else {
            throw new \Exception('ID to delete not given');
        }
    }
}

