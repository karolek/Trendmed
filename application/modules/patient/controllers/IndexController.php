<?php
class Patient_IndexController extends Me_User_Controllers_LoginController
{
   protected $_userModel = 'Patient_Model_Patient'; // class name of the user model

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
}