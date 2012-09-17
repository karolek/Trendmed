<?php
/**
 * This controllers takes care of login, logout and password recovery actions 
 */
use \Trendmed\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Admin_AdsController extends Zend_Controller_Action
{
    protected $_em; // doctrine entity manager
    
    public function init()
    {
        $this->_em = $this->getDoctrineContainer()->getEntityManager();
        $this->view->headTitle($this->view->translate('Ads management'));
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
     * List all zones with it's banners and stats
     */
    public function indexAction()
    {
        $ads = $this->_em->getRepository('\Trendmed\Entity\BannerAd')
            ->findAll();
        $zones = \Zend_Registry::get('config')->ads->zone;
        $this->view->ads = $ads;
        $this->view->zones = $zones;
    }

    /**
     * Displays the form and handles adding and editing of the banner
     *
     * @throws \Exception
     */
    public function saveBannerAction()
    {
        $form = new Admin_Form_BannerAd();
        $config = \Zend_Registry::get('config');
        $form->setZones($config->ads->zone->toArray());
        $request = $this->getRequest();
        $id     = $request->getParam('id', null);

        if($id) { // edit action
            $entity = $this->_em->find('\Trendmed\Entity\BannerAd', $id);
            if(!$entity) throw new \Exception('Wrong params supplied');
            $form->populate(array('id' => $id));
            $form->populate(array(
                'description'   => $entity->getDescription(),
                'target'        => $entity->getTarget(),
                'type'          => $entity->getType(),
                'zone'          => $entity->getZone(),
                'isActive'      => $entity->isActive(),
                'openIn'        => $entity->getOpenIn(),
            ));
            $form->removeElement('file'); // no edit file is possible to not mess the stats
            // stats correspond to concrete image
            $form->removeElement('type');
            $this->view->banner = $entity;
        } else { // new action
            $entity = new \Trendmed\Entity\BannerAd();
        }

        if($request->isPost()) {
            $post = $request->getPost();
            if($form->isValid($post)) {
                // remember, than when you have upload, You can't use getValues,
                // beacose it will clear superglobals
                // and process will not work
                if($_FILES['file']) {
                    $dir = $entity->processFile();
                    $entity->setFile($dir);
                }
                //$values = $form->getValues();
                $entity->setOptions($post);
                $this->_em->persist($entity);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'Banner info changed'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('error' => 'Please correct the form error'));
            }
        }

        $this->view->form = $form;
    }

    /**
     * Handles deletion of a banner
     *
     * @throws \Exception
     */
    public function deleteBannerAction()
    {
        $request = $this->getRequest();
        $id     = $request->getParam('id', null);
        if($id) { // edit action
            $entity = $this->_em->find('\Trendmed\Entity\BannerAd', $id);
            if(!$entity) throw new \Exception('BannerAd Id: '.$id.' not found');
            $this->_em->remove($entity);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array('success' => 'Banner ad removed'));
            $this->_helper->Redirector('index');
        } else { // new action
            throw new \Exception('Invalid params in '.__FUNCTION__);
        }
    }
}

