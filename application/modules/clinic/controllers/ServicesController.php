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
     * Adding NEW service to clinic
     */
    public function addServiceAction()
    {
        $request    = $this->getRequest();
        $form       = new Clinic_Form_Service();
        $config     = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');

        if($request->isPost()) {
            $post = $request->getPost();
            # we need to add categories to form (in view their are added by ajax) to bypass validation
            $form->addCategoriesToSelect(
                $form->getElement('subCategory'),
                $post['mainCategory'],
                $this->_em->find('\Trendmed\Entity\Category', $post['subCategory'])
                );

            if($form->isValid($post)) {
                $service = new \Trendmed\Entity\Service();
                # ok, we can now take care of photos upload
                # there is a problem when service is not saved then sortable on service photo will not work
                # this will couse strange problem, so either first save service or remove sortable from service photos
                $photos = array();
                for($i = 0; $i <= $config->services->photo->limit; $i++ ) {
                    if(!empty($_FILES['photo'.$i]['tmp_name'])) {
                        $photo = new \Trendmed\Entity\ServicePhoto();
                        $photo->processUpload($_FILES['photo'.$i]);
                        $photos[] = $photo;
                        # we we will add photos to service after first flush to use sorting on photos
                    }
                    unset($_FILES['photo'.$i]);
                }

                # getValues must be after processUpload or it will not work
                $values = $form->getValues();
                # ok, new fill the service object
                $service->setOptions($values);

                # translatable fields
                foreach ($config->languages as $lang) {
                    if (!$lang->default) {
                        //translations
                        $repository->translate(
                            $service, 'description', $lang->code,
                            $values['description_'.$lang->code]
                        );
                    }
                }
                # clinic is current logged user
                $service->setClinic($this->_helper->LoggedUser());

                # we must fetch selected category
                $category = $this->_em->find('\Trendmed\Entity\Category', $post['subCategory']);
                $service->setCategory($category);

                # now, let's save service to database
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

                # all done, service added!
                $this->_helper->FlashMessenger(array('success' => 'Nowa usługa została dodana do twojego profilu'));
                $this->_helper->Redirector('index');

            } else {
                $this->_helper->FlashMessenger(array('error' => 'Please correct the form'));
            }
        }

        $this->view->headTitle('Dodawanie nowej usługi');

        $this->view->form = $form;

        if ($config->clinics->useCke == 1) {
            $this->_helper->EnableCke($this->view, array());
        }
        $this->view->config = $config;

        $this->view->headScript()->appendFile('/js/servicesSelect.js');
    }

    /**
     * Edit clinic service
     *
     * @throws \Exception
     */
    public function editServiceAction()
    {
        $request    = $this->getRequest();
        $id         = $request->getParam('id', null);
        $service = $this->_em->find('\Trendmed\Entity\Service', $id);
        if (!$service) throw new \Exception('Bad parameters in '.__FUNCTION__);

        $form       = new Clinic_Form_Service();
        $config     = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');

        # selecting proper category in categories menu
        $form->addCategoriesToSelect($form->getElement('subCategory'),
            $service->getCategory()->parent->id,
            $service->getCategory(),
            $this->_helper->LoggedUser()->usedCategories()
        );

        $form->addCategoriesToSelect($form->getElement('mainCategory'),
            0,
            $service->getCategory()->parent
        );

        # translations for this object
        $translations = $repository->findTranslations($service);
        # informing form how many photos do clinic used allready
        $form->setPhotosUsed($service->getPhotos()->count());

        foreach ($translations as $transCode => $trans) {
            $form->setDefault('description_'.$transCode, $trans['description']);
        }

        # populate the form
        $form->populate($service->toArray());

        if ($request->isPost()) {
            $post = $request->getPost();
            // this is to baypass the validation
            $form->addCategoriesToSelect(
                $form->getElement('subCategory'),
                $post['mainCategory']);

            # fetching category that user selected
            $category = $this->_em->find('\Trendmed\Entity\Category', $post['subCategory']);

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
                $service->setCategory($category);

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

            }
        }

        // TODO: we must remove from the categories of service, allready added categories
        $this->view->service = $service;

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view, array());
        $this->view->config = $config;

        $this->view->headScript()->appendFile('/js/servicesSelect.js');
        $this->view->headTitle('Edycja usługi');

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
        $this->_helper->Redirector('edit-service', 'services', 'clinic', array('id' => $serviceId));
    }
}
