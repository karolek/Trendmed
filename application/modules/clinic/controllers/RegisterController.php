<?php
class Clinic_RegisterController extends Me_User_Controllers_RegisterController
{
    protected $_roleName = 'clinic'; // role name that we want to register new user
    protected $_userModel = 'Clinic_Model_Clinic'; // class name of the user model


    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        parent::indexAction();
        // action body
    }

    public function getRegistrationForm()
    {
        return new Clinic_Form_Registration();
    }
}

