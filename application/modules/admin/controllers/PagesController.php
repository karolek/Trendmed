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
    public function getDoctrineContainer()
    {
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
        $config = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('Gedmo\Translatable\Entity\Translation');
        $form = new Admin_Form_Page();

        if ($pageId) { // means we are in edit mode
            $entity = $this->_repo->find($pageId);
            $translations = $repository->findTranslations($entity);

            $form->setDefault('id', $pageId);
            $form->populate(array(
                'title' => $entity->getTitle(),
                'content' => $entity->getContent(),
                'type' => $entity->getType(),

            ));

            if ($entity->getLeadPhoto()) {
                $form->addPhoto($entity->getLeadPhoto(), $this->view->getHelper('ShowPhoto'));
            }

            foreach ($translations as $transCode => $trans) {
                $form->setDefault('title_' . $transCode, $trans['title']);
                $form->setDefault('content_' . $transCode, $trans['content']);
            }
            // if page is "sponsored" add sponsored clinic element to form
            $this->view->headTitle('Edit page');
            $form->setDefault('sponsoredByClinic', $entity->getSponsoredByClinic()->id);
        } else {
            $this->view->headTitle('Add new page');
            $entity = new \Trendmed\Entity\Page;
        }

        if ($req->isPost()) {
            $post = $req->getPost();
            if ($form->isValid($post)) {
                // we need to process the lead photo before $form->getValues as it clears the $_FILES array
                if ($_FILES['leadPhoto']['tmp_name']) {
                    $articlePhoto = new \Trendmed\Entity\ArticlePhoto();
                    $articlePhoto->processUpload($_FILES['leadPhoto']);
                    // remove old photo
                    if ($entity->getLeadPhoto()) {
                        $this->_em->remove($entity->getLeadPhoto());
                        $this->_em->flush();
                    }
                    $articlePhoto->setArticle($entity);

                    $this->_em->persist($articlePhoto);

                }
                $values = $form->getValues();
                unset($values['leadPhoto']);
                $sponsoredByClinicId = $values['sponsoredByClinic'];
                unset($values['sponsoredByClinic']);

                if (!empty($sponsoredByClinicId)) {
                    $sponsoredByClinic = $this->_em->find('\Trendmed\Entity\Clinic', $sponsoredByClinicId);
                    if (!$sponsoredByClinic) throw new \Exception('Indicated sponsored clinic does not exist');
                    $entity->setSponsoredByClinic($sponsoredByClinic);
                }

                $entity->setOptions($values);

                foreach ($config->languages as $lang) {

                    if ($lang->default == true) { // we must add default values to our main entity
                        $entity->setTitle($values['title']);
                        $entity->setContent(
                            $values['content']
                        );
                        continue;
                    }
                    $repository->translate(
                        $entity, 'title', $lang->code,
                        $values['title_' . $lang->code]
                    );
                    $repository->translate(
                        $entity, 'content', $lang->code,
                        $values['content_' . $lang->code]
                    );
                }

                $this->_em->persist($entity);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Page has been saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Please, correct any errors in the form'));
            }
        }

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view, array('content', 'content_de_de', 'content_en_GB'), 'AdminToolbar');
        // adding js for sponsored clinic
        $this->view->headScript()->appendFile('/js/admin/pages.js');
    }

    public function deletePageAction()
    {
        $request = $this->getRequest();

        $this->view->headTitle('Delete page');
        if ($request->getParam('id')) {
            $entityId = $request->getParam('id');
            $entity = $this->_repo->find($entityId);
            if (!$entity) throw new \Exception('No entity found');
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
        if ($request->getParam('id')) {
            $entityId = $request->getParam('id');
            $entity = $this->_repo->find($entityId);
            if (!$entity) throw new \Exception('No entity found');

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

