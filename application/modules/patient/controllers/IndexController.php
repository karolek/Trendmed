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

    public function activateUserAction()
    {
        $request = $this->getRequest();
        $this->view->headTitle('Aktywacja konta');
        $token      = $request->getParam('token'); // either get or post param

        // we have to find user by token
        $user = $this->_em->getRepository('Trendmed\Entity\Patient')->findOneByToken($token);
        if(!$user) {
            throw new \Exception('No user with this token found in DB', 500);
        }

        // checking if token is valid
        if (false === $user->tokenIsValid($token)) {
            $newLink = '<a href="'. $this->_helper->url('resend-activation-link') . '">new activation link</a>';
            $this->_helper->FlashMessenger(array('error' => 'The token You have supplied in URL
            is either incorrect or not valid anymore, please generate '.$newLink));
            $this->_helper->Redirector('index', 'index', 'user');
        } else {
            // we can now activate the user
            $user->activate();
            $this->_em->persist($user);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array('success' => 'Welcome on board! Your account is
            now fully confirmed and you can log in.'));
            $this->_helper->Redirector('index', 'index', 'patient');
        }
    }

    public function resendActivationLinkAction()
    {
        $this->view->headTitle('Resend Activation link');
        $request = $this->getRequest();
        $form = new User_Form_PasswordRecovery();
        $form->getElement('Recover')->setLabel('Resend');
        if($request->isPost()) {
            $username = $request->getParam('username', null);

            // we have to find user by slug
            $user = $this->_em->getRepository('IAA\Entity\User')->findOneByEmailaddress($username);
            if($user->isActive() === true) {
                $this->_helper->FlashMessenger(
                    array('error' => 'Your are already an confirmed user. No need to active your account twice.')
                );
                $this->_helper->Redirector('index', 'index', 'user');
            }

            if(!$user) {
                throw new \Exception('No user with this slug found in DB', 500);
            }

            $user->sendWelcomeEmail();
            $this->_em->persist($user);
            $this->_em->flush();

            $this->_helper->FlashMessenger(array('success' => 'Activation link resend to '.$user->getEmailaddress()));
            $this->_helper->Redirector('index', 'index', 'user');
        }

        $this->view->form = $form;
    }

}