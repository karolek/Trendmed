<?php
/**
 * The idea of this class is to provice simple controller for every 
 * registration proccess for every user You would even need.
 * 
 * You need to suply vaues for two variables $_roleName with will be the name
 * from aclrole (I will register new user with that role) and a $_userMode class
 * name. This object will be saved using the mapper.  
 */
class Patient_RegisterController extends Me_User_Controllers_RegisterController
{
    protected $_userModel = '\Trendmed\Entity\Patient'; // class name of the user model
    protected $_messageSuccessAfterRegistration = array(
        'success' => 'Dziękujemy za rejestracje, zapraszamy do rezerwacji usług'
    );
    
	public function getRegistrationForm()
	{
        return new Patient_Form_PatientRegistration();
	}
}

