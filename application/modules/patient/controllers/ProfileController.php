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

    }
}