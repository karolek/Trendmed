<?php
/**
 * Clinic profile controller. Handles profile editing of clinic, 
 * managing services, account data and so on
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_ProfileController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();

    }

    /** 
     * Dashboard for logged clinic. Shows latest reservations and infos.
     * Also, shows info about completition of adding information to profile
     */
    public function indexAction()
    {
        $this->view->headTitle('Clinic dashboard');
        $this->view->clinic = $this->_helper->LoggedUser();

        # fetching clinics reservations
        $repo = $this->_em->getRepository('\Trendmed\Entity\Reservation');
        $reservations = $repo->fetchAllClinicReservations($this->_helper->LoggedUser());
        #config needed for reservation status change actions
        $this->view->config = \Zend_Registry::get('config');

        $this->view->reservations = $reservations;
    }

    /**
     * Edits this part of clinics description that is visible to patients
     * like pictures, description
     * @throws Exception if no logged user in the system
     */
    public function editDescriptionAction()
    {
        $this->view->headTitle('Edycja opisu placówki');

        $request = $this->getRequest();

        $this->_helper->EnableCke($this->view, array(), 'ClinicToolbar');


        $form = new Clinic_Form_ClinicProfile_MultiLangDesc();
        $form->setAction($this->_helper->url('edit-description'));
        $form->addElement(new \Zend_Form_Element_Submit('Zapisz'));

        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new Exception(
                'No logged user found, so now edit Details is possible'
            );
        }

        // populate form
        $config = \Zend_Registry::get('config');
        $this->view->config = $config;
        $form->populateFromUser($user);

        // init the form for new photo
        $photoForm = new Clinic_Form_ClinicPhoto();
        $photoForm->setAction($this->view->url(
            array(
                'action' => 'add-photo'
            )
        ));
        $this->view->photoForm = $photoForm;

        if ($request->isPost()) { // saveing form with description
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                foreach ($config->languages as $lang) {
                    if ($lang->default == 1) { // we must add default values to our main entity
                        $user->setCustomPromos(
                            $values['customPromos_'.$lang->code]);
                        $user->setDescription(
                            $values['description_'.$lang->code]
                        );
                    } else {
                        $repository->translate(
                            $user, 'customPromos', $lang->code,
                            $values['customPromos_'.$lang->code]
                        );
                        $repository->translate(
                            $user, 'description', $lang->code,
                            $values['description_'.$lang->code]
                        );
                    }
                }
                $this->_em->persist($user);
                $this->_em->flush();

                $this->_helper->FlashMessenger(
                    array('success' => 'You have changed Your details')
                );
                $this->_helper->Redirector(
                    'index', 'profile', 'clinic', array('slug' => $user->getSlug())
                );
            }
        }
        $this->view->form = $form;
    }

    /**
     * Saveing photo upload
     * @throws Exception
     */
    public function addPhotoAction()
    {
        $request = $this->getRequest();
        $clinic = $this->_helper->LoggedUser();
        if($request->isPost()) {
            $photo = new \Trendmed\Entity\ClinicPhoto();
            $clinic->addPhoto($photo);

            // doing all the upload magic
            $photo->processUpload();

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
                    'edit-description'
                );
            }
        } else {
            throw new \Exception('Add photo should be a post request');
        }
    }

    /**
     * Action for changing password of clinic account
     *
     * @throws \Exception
     */
    public function changePasswordAction() 
    {
        $form = new Clinic_Form_NewPassword();
        // this form will not use token to authorize
        $form->setNotFromToken();

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new \Exception('No logged user found, so now edit Details
               is possible');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            // we should authorize the user with an old password
            //checking password
            if(!$user->authorize($post['old_password'])) {
                $form->getElement('old_password')->addError('Password does not match with Your account password');
            }
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $user->setPassword($values['password']);
                $user->sendNewPasswordEmail();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'You have changed Your password'));
            } else {
                $form->getElement('old_password')->markAsError();
                $this->_helper->FlashMessenger(array('warning' => 'Please fix the errors in the form'));
            }
        }
        $this->view->form = $form;
        $this->view->headTitle('Change my password');

    }

    /**
     * Editing clinic public logo
     *
     */
    public function editLogoAction()
    {
        $this->view->headTitle($this->view->translate('Edit my logo'));
        $this->view->user = $user = $this->_helper->LoggedUser();

        $request = $this->getRequest();
        $form = new Clinic_Form_ClinicProfile_Logo();

        if($request->isPost()) {
            $post = $request->getPost();
            if($form->isValid($post)) {
                $user->processLogo();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger('Logo uploaded.');
                $this->_helper->Redirector('edit-logo');
            } else {
                $this->_helper->FlashMessenger('Fix errors with the file');
            }
        }
        $this->view->form = $form;
    }

    public function deleteLogoAction()
    {
        $user = $this->_helper->LoggedUser()->deleteLogo();
        $this->_em->persist($user);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'Your logo has been deleted'));
        $this->_helper->Redirector('edit-logo');
    }

    public function deleteClinicPhotoAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $photo = $this->_em->find('\Trendmed\Entity\ClinicPhoto', $id);

        if($photo->clinic->id != $this->_helper->LoggedUser()->id) {
            throw new \Exception('Security breach, trying to delete not Your photo');
        }

        $this->_em->remove($photo);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'Zdjęcie zostało usunięte'));
        $this->_helper->Redirector('edit-description', 'profile');
    }


    /**
     * This is for saveing form with clinic address data
     * @throws \Exception
     */
    public function editAddressAction() {
        $this->view->headTitle('Edycja danych adresowych');
        $request = $this->getRequest();
        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new Exception(
                'No logged user found, so now edit Details is possible'
            );
        }

        // init the form for clinic addres data
        $form = new Clinic_Form_ClinicProfile_Account();
        // populating with hydrated to array logged user
        $form->populate($this->_em->getRepository('\Trendmed\Entity\Clinic')->findOneAsArray($user->getId()));
        $form->setAction($this->view->url(array(
            'action'        => 'edit-address',
            'controller'    => 'profile',
            'module'        => 'clinic'
        )));

        if ($request->isPost()) { // saveing form with description
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $user->setOptions($values);

                $this->_em->persist($user);
                $this->_em->flush();

                $this->_helper->FlashMessenger(
                    array('success' => 'You have changed Your details')
                );
                $this->_helper->Redirector(
                    'index', 'profile', 'clinic'
                );
            }
        }
        $this->view->form = $form;
    }

    public function editSettingsAction()
    {
        $this->view->headTitle('Zarządzanie ustawieniami placówki');
        $request    = $this->getRequest();
        $clinic     = $this->_helper->LoggedUser();
        $config     = \Zend_Registry::get('config');
        $form       = new \Clinic_Form_Settings();
        $form->populate(array(
            'wantBill' => $clinic->wantBill,
            'backAccount' => $clinic->bankAccount,
        ));

        if($request->isPost())
        {
            $post = $request->getPost();
            if($form->isValid($post)) {
                $values = $form->getValues();
                $clinic->setWantBill($values['wantBill']);
                $clinic->setBankAccount($values['bankAccount']);
                $this->_em->persist($clinic);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Ustawienia zostały zapisane'));
                $this->_helper->Redirector('index');

            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Popraw błędy w formularzu'));
            }
        }

        $this->view->form = $form;

    }
}
