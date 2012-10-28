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

    public function facebookLoginAction()
    {
        // first we check if we have an authorized user in Facebook
        // initializing facebook Api
        $config = \Zend_Registry::get('config');
        $fbConfig = array();
        $fbConfig['appId']  = $config->facebook->appId;
        $fbConfig['secret'] = $config->facebook->appSecret;
        $fbConfig['fileUpload'] = false; // optional
        $facebook = new Facebook($fbConfig);
        $log = \Zend_Registry::get('log');
        $log->debug('facebook appId '.$config->facebook->appId);

        // getting facebook user Id
        $uid = $facebook->getUser();
        // if user Id is > 0 than we have authorized user, if its < 1 than we have an error
        if($uid < 1) {
            throw new \Exception('There is no authorized user on Facebook, user Id returned from API: '.$uid);
        } else { // let's fetch some user information
            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
                $user_profile = $facebook->api('/me','GET');
            } catch(FacebookApiException $e) {
                // If the user is logged out, you can have a
                // user ID even though the access token is invalid.
                // In this case, we'll get an exception, so we'll
                // just ask the user to login again here.
                $login_url = $facebook->getLoginUrl();
                $this->_helper->FlashMessenger(
                    array('warning' => 'Please <a href="' . $login_url . '">login.</a>')
                );
                $log->debug($e->getType());
                $log->debug($e->getMessage());
                $this->_helper->Redirector('index', 'index', 'default');
            }
        }

        // lets check if user had registered on IAA
        $repo = $this->_em->getRepository('\Trendmed\Entity\Patient');
        // first, we check if we have a user with given Facebook userId.
        $log->debug('Looking for a user with Facebook ID: '.$uid);
        $user = $repo->findOneByFacebookId($uid);
        if(!$user) { // no user with this ID found
            // we must also check if user did not registered before with his e-mail
            // if so, user can connect his account with facebook by typing password on seperate action
            $user = $repo->findOneByLogin($user_profile['email']);
            if($user) { // we have found this e-mail in database
                // we will now redirect user to another action to connect his old account
                // saveing to session for later use
                $session = new \Zend_Session_Namespace('facebook_temp_user');
                $session->id                = $user->getId();
                $session->facebookUserId    = $uid; //facebook uId
                $this->_helper->Redirector('connect-accounts', 'index', 'patient');
            }
        }

        // we either didnt found any user, user is redirected to connect his account or we have found an
        // face account. Let us start with a new user, the one we didnt found in the database, let's register him
        if(!$user) { // user did connect accounts
            $log->debug('User did not register');
            // creating new user
            $user = new \Trendmed\Entity\Patient;
            $user->setEmailaddress($user_profile['email']);
            $user->setIsActive(true);
            $user->setName($user_profile['first_name'] .' '. $user_profile['last_name']);
            // setting up password with the facebok ID
            $user->setFacebookId($user_profile['id']);
            $user->setPassword($user_profile['id']); // this is just for requirments
            $role = $this->_em->getRepository('\Trendmed\Entity\Role')->findOneByName('patient');
            if (!$role) {
                throw new Exception('Role for user not found');
            }
            $user->setRole($role);
            //$user->sendWelcomeEmail(); // we dont want to send welcome email couse e-mail is allready confirmed by facebook

            $this->_em->persist($user);
            // we need to have user ID in DB before authorize so we need to flush
            $this->_em->flush();
        }
        // OK. Now we have user entity that has facebookId attached, so we can authorize it via facebook

        $result = $user->authorizeViaFacebook($uid);
        // authorizing the user
        if ($result === true) { // access granted
            // messages and redirections
            $user->setLastLoginTime(new \DateTime("now"));
            $user->activate();
            $this->_em->persist($user);
            $this->_em->flush();

            $log->debug('Access Granted');
            $this->_helper->FlashMessenger(array(
                'success' => 'Logged via facebook successful',
            ));
            $this->_helper->Redirector('index', 'index', 'default');
        } else {
            $log->debug('Access Frobidden');
        }

        //user should be redirected by now, if not, then something went wrong
        throw new \Exception('Failed to authorize user in facebook-login-action');
    }

    public function connectAccountsAction()
    {
        $this->view->headTitle('Connect accounts');
        $request    = $this->getRequest();
        $form       = new \Patient_Form_Connect();
        $log        = \Zend_Registry::get('log');
        if($request->isPost()) {
            $post = $request->getPost();
            if($form->isValid($post)) {
                $log->debug('connect form is valid');
                // double check, fetching from session
                $session        = new \Zend_Session_Namespace('facebook_temp_user');
                $userId         = $session->id;
                $facebookUserId = $session->facebookUserId;
                $log->debug('userID from session: ' . $userId);
                $log->debug('FBuserID from session: ' . $facebookUserId);

                // checks params
                if(empty($userId) or empty($facebookUserId))
                    throw new \Exception(
                        'UserID or Facebook user Id not in session (userID: '.$userId.' facebookID: '.$facebookUserId.')'
                    );

                $user = $this->_em->find('\Trendmed\Entity\Patient', $userId);

                if(!$user) {
                    throw new \Exception('User with ID from session not found in DB, user ID: '.$userId);
                }
                $values = $form->getValues();
                // now we check is user can authorize, logging it if yes
                if($user->authorize($values['password'])) { // user authorized
                    // user is now logged in and we can connect his account by adding him a FBUserId
                    $user->setFacebookId($facebookUserId);
                    $this->_em->persist($user);
                    $this->_em->flush();
                    $this->_helper->FlashMessenger(array('success' => 'Accounts connected successfully'));
                    $this->_helper->Redirector('index', 'profile', 'patient');
                } else {
                    $this->_helper->FlashMessenger(array('warning' => 'Wrong password supplied'));
                }

            } else {
                $this->_helper->FlashMessenger(array('success' => 'Please fix the errors in the form'));
            }
        }
        $this->view->form = $form;
    }

}