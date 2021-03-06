<?php
class Patient_IndexController extends Me_User_Controllers_LoginController
{
   protected $_userModel = '\Trendmed\Entity\Patient'; // class name of the user model

   public function getLoginForm()
   {
       $form = new Patient_Form_Login();
       $form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'index/loginForm.phtml'))
        ));
       return $form;
   }
   
   public function getPasswordRecoveryForm()
   {
       $form = new Patient_Form_PasswordRecovery();
       return $form;
   }
   
   public function getNewPasswordForm() {
       $form = new Patient_Form_NewPassword();
       return $form;
   }
}