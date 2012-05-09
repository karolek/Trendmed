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
     */
    public function indexAction()
    {
    
    }

    /**
     * Edits this part of clinics description that is visible to patients
     * like logo, pictures, description
     * @throws Exception if no logged user in the system
     */
    public function editProfileAction()
    {
        $request = $this->getRequest();

        $this->view->headTitle('Clinic profile');
        $form = new Clinic_Form_ClinicProfile();
        $form->addSubForm(new Clinic_Form_ClinicProfile_Logo(), 'logo');
        $form->addElement(new Zend_Form_Element_Submit('Save'));
        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new Exception(
                'No logged user found, so now edit Details is possible'
            );
        }
        //$form->populate($user->toArray());
        if ($request->isPost()) {
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
                    'profile', 'profile', 'user', array('id' => $user->id)
                );
            }
        }
        $this->view->form = $form;
    }
    
    /**
     * Edit those parts of the clinic description that are use for 
     * administration purpuses, like bank account no., 
     * want bill setting, rep email or address.
     */
    public function editAccountAction()
    {


    }
    
    /**
     * Place to edit, add, modify clinics services
     */
    public function manageServicesAction()
    {
        $request = $this->getRequest();
        $form = new Clinic_Form_Service();
        $this->view->form = $form;
    }
    
    /**
     * Action for changing password of clinic account
     */
    public function changePasswordAction() 
    {
        $this->view->headTitle($this->view->translate('Password change'));
        $form = new Clinic_Form_NewPassword();

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new \Zend_Exception(
                'No logged user found, so now edit Details is possible'
            );
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $user->setPassword($values['password']);
                $user->sendNewPasswordEmail();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(
                    array('success' => 'You have changed Your password')
                );
            }
        }
        $this->view->form = $form;
    }
}
