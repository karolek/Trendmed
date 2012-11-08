<?php
class Patient_ProfileController extends Zend_Controller_Action
{
    public function init()
    {
        // enabling ajax for some actions
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-favorite-clinic', 'html')
            ->initContext();
        $this->_em =  $this->_helper->getEm();

    }
    
    /**
     * This is patient dashboard, displays he's reservations and fav. clinics
     */
    public function indexAction()
    {
        # fetching patient reservations
        $repo = $this->_em->getRepository('\Trendmed\Entity\Reservation');
        $reservations = $repo->fetchAllPatientReservations($this->_helper->LoggedUser());

        $this->view->reservations = $reservations;


        # config to use in view
           $this->view->config = \Zend_Registry::get('config');

        $this->view->headTitle('My reservations');
    }

    /**
     * Allows to add or remove a clinic from favorites of logged user.
     * Prepared for ajax and normal post request
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function addFavoriteClinicAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $user = $this->_helper->LoggedUser();
            $values = $request->getPost();
            $entityName = $values['entity'];
            $entityName = '\Trendmed\Entity\Clinic';
            $entityId   = $request->getParam('entity_id');
            // Im leaving up to ACL that only patient should be able to add fav clinic
            if (!$user instanceof \Trendmed\Entity\Patient) {
                throw new \InvalidArgumentException('Only logged Patients cad add a fav. clinic');
            }
            $clinic = $this->_em->find($entityName, $entityId);
            if(!$clinic)
                throw new \Exception('No entity with ID ' . $entityId . ' found');
            // result will tell us if user added or removed clinic (unlike/like)
            $result = $user->toggleFavoriteClinic($clinic);

            $this->_em->persist($user);
            $this->_em->persist($clinic);
            $this->_em->flush();
            $this->view->clinic     = $clinic;
            $this->view->user       = $user;

            if(!$request->isXmlHttpRequest()) { // if this is not AJAX request then
                // add some messege and redirect user
                $this->_helper->FlashMessenger(array('success' => $this->view->translate('Changes saved')));
                $this->_helper->Redirector('index');

            }

        } else {
            throw new \Exception('Request should be POST');
        }
    }

    public function editDetailsAction()
    {
        $form = new \Patient_Form_Details();
        $patient = $this->_helper->LoggedUser();
        $request = $this->getRequest();

        // checking if client was on new reservation page before comming here
        $reservationInSession = new Zend_Session_Namespace('Reservation_Temp');

        if (isset($reservationInSession->clinic_id)) {
            $this->view->reservationOngoing = true;
            $this->view->reservation_clinic_slug = $reservationInSession->clinic_slug;
        }

        if($request->isPost()) {
            if($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $patient->setName($values['name']);
                $patient->setCountry($values['country']);
                $patient->setPhoneNumber($values['phoneNumber']);
                $patient->setTitle($values['title']);

                $this->_em->persist($patient);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'Your changes to the profile has been saved'));
            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Please, fix errors in the form'));
            }
        }


        // populate form with current logged user
        $form->populateFromObject($patient);

        $this->view->headTitle('Edit my details');
        $this->view->form = $form;
    }

    public function changePasswordAction()
    {
        $form = new Patient_Form_NewPassword();

        // informing the form that this change is done not from token but by logged user
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

    public function changeEmailAction()
    {
        $form = new Patient_Form_ChangeEmail();
        $request = $this->getRequest();

        if($request->isPost()) {
            $post = $request->getPost();
            $user = $this->_helper->LoggedUser();
            //checking password
            if(!$user->authorize($post['password'])) {
                $form->getElement('password')->addError('Password does not match with Your account password');
            }
            if($form->isValid($post)) {
                $values = $form->getValues();
                $user->setTempEmailAddress($values['emailaddress']);
                $user->setToken($user->generatePasswordRecoveryToken()->getToken());
                $user->sendNewEmailAddressEmail();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Please confirm Your new e-mail address'));
            } else {
                $form->getElement('password')->markAsError();
                $this->_helper->FlashMessenger(array('warning' => 'Please fix the errors in the form    '));
            }
        }
        $this->view->headTitle('Change e-mail address');
        $this->view->form = $form;
    }

    /**
     * Manages user newsletter status
     */
    public function newsletterSignupAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost();
            $user = $this->_helper->LoggedUser();
            if($post['sign_up'] == 1) {
                $user->setIsNewsletterActive(true);
                $this->_helper->FlashMessenger(array('success' => 'You have been signed up for the newsletter'));
            } else {
                $user->setIsNewsletterActive(false);
                $this->_helper->FlashMessenger(array('success' => 'You have been signed out from out newsletter system'));
            }
            $this->_em->persist($user);
            $this->_em->flush();
        }
        $this->view->headTitle($this->view->translate('Newsletter sign up/sign out'));
    }
}