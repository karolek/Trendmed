<?php
class Admin_IndexController extends Me_User_Controllers_LoginController
{
    protected $_userModel = 'Admin_Model_Admin'; // class name of the user model
    protected $_messageAfterLogin = array(
      'success' => 'You have successfully logged in'  
    );
    protected $_messageAfterLogout = array(
      'success' => 'You have successfully logged in'  
    );
    protected $_redirectAfterLogin = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default',
    );
    protected $_redirectAfterLogout = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default',
    );
    protected $_redirectAfterNewPassword = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default'
    );
   public function getLoginForm()
   {
       $form = new Admin_Form_Login();
       $form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'index/loginForm.phtml'))
        ));
       return $form;
   }
   
   public function getPasswordRecoveryForm()
   {
       $form = new Admin_Form_PasswordRecovery();
       return $form;
   }
   
   public function getNewPasswordForm() {
       $form = new Admin_Form_NewPassword();
       return $form;
   }
   
}

