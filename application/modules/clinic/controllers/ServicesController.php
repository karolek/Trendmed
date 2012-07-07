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
    public function editServicesAction()
    {
        $request = $this->getRequest();
        $form = new Clinic_Form_Service();
        $id = $request->getParam('id', null);
        $config = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');
        $this->view->headScript()->appendFile('/js/servicesSelect.js');
        $this->view->headScript()->appendFile('/js/jquery.html5uploader.min.js');


        if ($id) { //edit
            $service = $this->_em->find('\Trendmed\Entity\Service', $id);
            if (!$service) throw new \Exception('Bad parameters');
            $form->populate($service->toArray());
            $form->populate(
                array(
                    'id'            => $service->getId(),
                    'mainCategory'   => $service->getCategory()->parent->id,
                )
            );
            $form->addCategoriesToSelect('subCategory', $service->getCategory()->parent->id, $service->getCategory()->getId());
            $translations = $repository->findTranslations($service);

            foreach($translations as $transCode => $trans) {
                $form->setDefault('description_'.$transCode, $trans['description']);
            }
            $this->view->headTitle('Edycja usługi');
            // fetching photos
            $this->view->photos = $service->getPhotos();
        } else { // new
            $service = new \Trendmed\Entity\Service();
            $this->view->headTitle('Dodawanie usługi');
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            // this is to baypass the validation
            $form->addCategoriesToSelect('subCategory', $post['mainCategory'], $post['subCategory']);

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
                $service->setCategory($this->_em->find('\Trendmed\Entity\Category', $values['subCategory']));

                $session = new \Zend_Session_Namespace('service_photos_'.$this->_helper->LoggedUser()->getId());
                $log = \Zend_Registry::get('log');
                if(is_array($session->photos)) {
                    $log->debug('sa zdjecia w sesji');
                    foreach ($session->photos as $photo) {
                        $photo->setService($service);
                        $service->addPhoto($photo);
                        $this->_em->persist($photo);
                    }
                    unset($session->photos);
                } else {
                    $log->debug('nie ma zdjec w sesji');
                }

                $this->_em->persist($service);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'Changes saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('error' => 'Please correct the form'));
                // we have to populate the subcategory form again
                $values = $form->getValues();
                // we must fetch photos from session if any to display them to user
                $session = new \Zend_Session_Namespace('service_photos_'.$this->_helper->LoggedUser()->getId());
                if(is_array($session->photos)) {
                    $this->view->photos = $session->photos;
                }

            }
        }


        // TODO: we must remove from the categories of service, allready added categories
        $this->view->service = $service;

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view, array());
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
        $this->view->headScript()->appendFile('/js/servicesSelect.js');

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

            // doing all the upload magic
            $photo->processUpload();
            $service->addPhoto($photo);
            $this->_em->persist($photo);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array('success' => 'Dodano nowe zdjęcie do usługi'));
        }
        $this->view->service = $service;
        $this->view->headTitle('Zdjęcia usługi');
        $this->view->form = $form;
    }

    public function deleteServicePhotoAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $photo = $this->_em->find('\Trendmed\Entity\ServicePhoto', $id);
        $serviceId = $photo->service->id;
        if($photo->service->clinic->id != $this->_helper->LoggedUser()->id) {
            throw new \Exception('Security breach, trying to delete not Your photo');
        }

        $this->_em->remove($photo);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'Zdjęcie zostało usunięte'));
        $this->_helper->Redirector('manage-service-photos', 'services', 'clinic', array('id' => $serviceId));
    }

    /**
     * For AJAX Request
     */
    public function addPhotoToServiceAction()
    {
        $request = $this->getRequest();
        $entryPhoto = new \Trendmed\Entity\ServicePhoto();
        $filename = $entryPhoto->processUpload();
        $session = new \Zend_Session_Namespace('service_photos_'.$this->_helper->LoggedUser()->getId());
        $session->photos[] = $entryPhoto;

        echo $filename;
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
