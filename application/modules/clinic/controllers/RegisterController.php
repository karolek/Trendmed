<?php
/**
 * Manages clinic registrion. 
 * Most of the logic is inherited from parent controller class. 
 * This parent class is also used by other modules to register e.g. patient.
 * Please reference to library\Me\User\Controllers\RegisterController.php
 * for more info about methods that are avaible here.
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_RegisterController extends \Me_User_Controllers_RegisterController
{
    /**
     * @var string Class name of the user model
     */
    protected $_userModel = 'Trendmed\Entity\Clinic';

    public function getRegistrationForm()
    {
        $form = new Clinic_Form_ClinicRegistration();
        $form->setDecorators(
            array(
                array('ViewScript', array(
                    'viewScript' => 'register/registrationForm.phtml'
                    )
                )
            )
        );
        return $form;
    }

    /**
     * Displays static content about plans and pricing for Clinics.
     * TODO: enter contet of this web page
     */
    public function infoAction()
    {
        
    }

}

