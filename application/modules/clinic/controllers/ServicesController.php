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

            # informing form how many photos do clinic used allready
            $form->setPhotosUsed($service->getPhotos()->count());

            foreach($translations as $transCode => $trans) {
                $form->setDefault('description_'.$transCode, $trans['description']);
            }
            $this->view->headTitle('Edycja usługi');

        } else { // new
            $service = new \Trendmed\Entity\Service();
            $this->view->headTitle('Dodawanie usługi');
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            // this is to baypass the validation
            $form->addCategoriesToSelect('subCategory', $post['mainCategory'], $post['subCategory']);

            if ($form->isValid($post)) {
                # there is a problem when service is not saved then sortable on service photo will not work
                # this will couse strange problem, so either first save service or remove sortable from service photos
                $photos = array();
                for($i = 0; $i <= $config->services->photo->limit; $i++ ) {
                    if(!empty($_FILES['photo'.$i]['tmp_name'])) {
                        $photo = new \Trendmed\Entity\ServicePhoto();
                        $photo->processUpload($_FILES['photo'.$i]);
                        $photos[] = $photo;
                    }
                }


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

                $this->_em->persist($service);
                $this->_em->flush();

                # now, after the servce is saved we can add photos,
                # we need it bo be saved in order to use SortableBehaviour
                if (count($photo) > 0) {
                    foreach ($photos as $photo) {
                        $service->addPhoto($photo);
                        $this->_em->persist($photo);
                    }
                    $this->_em->flush();
                }

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

        # adding form for new photos
        $photoForm = new Clinic_Form_ServicePhoto();
        # it will point to manageServicePhotosAction
        $photoForm->setAction($this->view->url(array(
            'action' => 'manage-service-photos'
        )));
        # adding id of service to form
        $photoForm->addElement('hidden', 'id', array('value' => $service->id));
        $this->view->photoForm = $photoForm;

        // TODO: we must remove from the categories of service, allready added categories
        $this->view->service = $service;

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view, array());
        $this->view->config = $config;

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
        $this->_helper->Redirector('edit-services', 'services', 'clinic', array('id' => $serviceId));
    }
}
