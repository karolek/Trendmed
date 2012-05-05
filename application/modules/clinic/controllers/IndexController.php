<?php
/**
 * This controller takes care of login/logout action for clinic.
 * It also has some nice actions for password recovery.
 * Please refer to parent class for more info about the functions.
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
class Clinic_IndexController extends Me_User_Controllers_LoginController
{
    /**
     * @var string Class name of the user model
     */
    protected $_userModel = '\Trendmed\Entity\Clinic';
    protected $_messageAfterLogin = array(
      'success' => 'Zostaleś zalogowany poprawnie'  
    );
    protected $_messageAfterLogout = array(
      'success' => 'Zostaleś wylogowany z systemu'  
    );
    protected $_redirectAfterLogin = array(
        'action'        => 'index',
        'controller'    => 'profile',
        'module'        => 'clinic',
    );
    protected $_redirectAfterLogout = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'clinic',
    );
    protected $_redirectAfterNewPassword = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'clinic'
    );
    
   public function getLoginForm()
   {
       $form = new Clinic_Form_Login();
       $form->setDecorators(
           array(array('ViewScript', 
               array('viewScript' => 'index/loginForm.phtml')))
       );
       return $form;
   }
   
   public function getPasswordRecoveryForm()
   {
       $form = new Clinic_Form_PasswordRecovery();
       return $form;
   }
   
   public function getNewPasswordForm() 
   {
       $form = new Clinic_Form_NewPassword();
       return $form;
   }
}

