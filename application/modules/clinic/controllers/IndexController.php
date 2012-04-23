<?php

class Clinic_IndexController extends Me_User_Controllers_LoginController
{
    protected $_userModel = 'Clinic_Model_Clinic'; // class name of the user model
    protected $_messageAfterLogin = array(
      'success' => 'Zostaleś zalogowany poprawne'  
    );
    protected $_messageAfterLogout = array(
      'success' => 'Zostaleś wylogowany z systemu'  
    );
    protected $_redirectAfterLogin = array(
        'action'        => 'index',
        'controller'    => 'index',
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
       $form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'index/loginForm.phtml'))
        ));
       return $form;
   }
   
   public function getPasswordRecoveryForm()
   {
       $form = new Clinic_Form_PasswordRecovery();
       return $form;
   }
   
   public function getNewPasswordForm() {
       $form = new Clinic_Form_NewPassword();
       return $form;
   }
}

