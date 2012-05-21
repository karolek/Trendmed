<?php
/**
 * Clinic profile controller. Handles profile editing of clinic, 
 * managing services, account data and so on
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_ServicesController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
        $this->view->headTitle('Twoje usługi');
    }

    /** 
     * Dashboard for logged clinic. Shows latest reservations and infos.
     */
    public function indexAction()
    {
        $this->view->services = $this->_helper->LoggedUser()->services;
    }


    /**
     * Place to edit, add, modify clinics services
     *
     * @throws \Exception
     */
    public function manageServicesAction()
    {
        $request = $this->getRequest();
        $form = new Clinic_Form_Service();
        $id = $request->getParam('id', null);
        $config = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');


        if ($id) { //edit
            $service = $this->_em->find('\Trendmed\Entity\Service', $id);
            if (!$service) throw new \Exception('Bad parameters');
            $form->populate(array('id' => $service->getId()));
            $form->populate($service->toArray());
            $this->view->headTitle('Edycja usługi');
        } else { // new
            $service = new \Trendmed\Entity\Service();
            $this->view->headTitle('Dodawanie usługi');
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $service->setOptions($values);

                foreach ($config->languages as $lang) {
                    if (!$lang->default) {
                        //translations
                        $repository->translate(
                            $service, 'description', $lang->code,
                            $values['description_'.$lang->code]
                        );
                    }
                }
                $service->setClinic($this->_helper->LoggedUser());
                $service->setCategory($this->_em->find('\Trendmed\Entity\Category', $values['categories']));
                $this->_em->persist($service);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'Changes saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('error' => 'Please correct the form'));
            }
        }


        // we must remove from the categories of service, allready added categories
        // TODO: przepisać to jakoś
        $result = $form->getElement('categories')->getMultiOptions();
        if( count($this->_helper->LoggedUser()->services) >0 ) { // if user has services remove categories from form
            foreach ($this->_helper->LoggedUser()->services as $clinicServices) {
                foreach($result as &$optgroup) {
                    if(isset($optgroup[$clinicServices->category->id]) ) { // clinic allready has this category service
                        // we have to check also if clinic is just not editing has service,
                        // if se we let him keep his catgegory on the form
                        // let's check if we are id "edit" mode
                        if($service->getId() > 0 ) { // yes we are
                            if($clinicServices->category->id != $service->getCategory()->getId()) {
                                unset($optgroup[$clinicServices->category->id]);
                            }
                        }
                    }
                }
            }
        }
        $form->getElement('categories')->setMultiOptions($result);
        $this->view->service = $service;

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view);
    }
    
    public function deleteServiceAction()
    {
        $request    = $this->getRequest();
        $serviceId  = $request->getParam('id', null);

        // fetching the service
        $repo = $this->_em->getRepository('\Trendmed\Entity\Service');
        $service = $repo->find($serviceId);
        if(!$service) throw new \Exception('no service found with id: '.$serviceId);

        // checking owner
        if($service->clinic->id != $this->_helper->LoggedUser()->id) {
            throw new \Exception('security breach, attempt to delete clinic by no owner');
        }

        // removing the service and it's photos
        $this->_em->remove($service);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'Your service has been deleted'));
        $this->_helper->Redirector('index');
    }

    public function manageServicePhotosAction()
    {
        $request    = $this->getRequest();
        $serviceId  = $request->getParam('id', null);

        // fetching the service
        $repo = $this->_em->getRepository('\Trendmed\Entity\Service');
        $service = $repo->find($serviceId);
        if(!$service) throw new \Exception('no service found with id: '.$serviceId);

        // checking owner
        if($service->clinic->id != $this->_helper->LoggedUser()->id) {
            throw new \Exception('security breach, attempt to delete clinic by no owner');
        }

        $form = new Clinic_Form_ServicePhoto();

        if($request->isPost()) {
            $photo = new \Trendmed\Entity\ServicePhoto();
            $clinic->addPhoto($photo);

            // doing all the upload magic
            $photo->processFile();

            $this->_em->persist($photo);
            $this->_em->persist($clinic);
            $this->_em->flush();

            if($request->isXmlHttpRequest()) {
                echo 'OK';
                $this->_helper->layout()->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $this->_helper->FlashMessenger(array(
                    'success' => 'New photo added'
                ));
                $this->_helper->Redirector(
                    'edit-profile'
                );
            }
        }
        $this->view->service = $service;
        $this->view->headTitle('Zdjęcia usługi');
        $this->view->form = $form;
    }
}
