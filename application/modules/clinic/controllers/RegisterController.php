<?php
class Clinic_RegisterController extends Me_User_Controllers_RegisterController
{
    protected $_userModel = 'Clinic_Model_Clinic'; // class name of the user model

    public function getRegistrationForm()
    {
        $form = new Clinic_Form_ClinicRegistration();
        $form->setDecorators(array(
    array('ViewScript', array('viewScript' => 'register/registrationForm.phtml'))
));
        return $form;
    }
}

