<?php
class Admin_IndexController extends Me_User_Controllers_LoginController
{
    protected $_userModel = '\Trendmed\Entity\Admin'; // class name of the user model
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
   
   public function setupAdminAction() {
        $em = $this->_helper->getEm();
        $repo = $em->getRepository('\Trendmed\Entity\Admin');
        $admin = $repo->findOneByLogin('admin');
        if (!$admin) {
            $admin = new \Trendmed\Entity\Admin;
            $admin->setLogin('admin');
            $admin->setPassword('admin');
            
            //$role = new \Trendmed\Entity\Role;
            $roleRepo = $em->getRepository('\Trendmed\Entity\Role');
            $role = $roleRepo->findOneByName('admin');
            if(!$role) {
                $role = new \Trendmed\Entity\Role;
                $role->setName('admin');
                $em->persist($role);
            }
            $admin->setRole($role);
            
            $em->persist($admin);
            $em->flush();
        }
        echo 'login with You admin account';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

}

