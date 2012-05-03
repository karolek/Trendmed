<?php

/**
 * ProfileController
 * 
 * @author
 * @version 
 */

class Clinic_ProfileController extends Zend_Controller_Action {
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->getInvokeArg('bootstrap')->getResource('doctrine')
                ->getEntityManager();

    }

    // dashboard for logged user
    public function indexAction()
    {
    
    }

    public function editDetailsAction()
    {
        $this->view->headTitle('User details change');
        $form = new User_Form_UserDetails;
        $loggedUserId = $this->_helper->LoggedUser();
        $user = $this->_em->getRepository('IAA\Entity\User')->findOneById($loggedUserId);
        if(!$user) {
            throw new Zend_Exception('No logged user found, so now edit Details
                is possible');
        }
        $form->populate($user->toArray());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if($form->isValid($post)) {
                $values = $form->getValues();
                $user->setOptions($values);
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'You have changed Your details'));
                $this->_helper->Redirector('profile', 'profile', 'user', array(
                    'id' => $user->id,
                ));
            }
        }
        $this->view->form = $form;
    }
    
    public function changePasswordAction() 
    {
        $this->view->headTitle($this->view->translate('Password change'));
        $form = new Clinic_Form_NewPassword();

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new Zend_Exception('No logged user found, so now edit Details
               is possible');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $user->setPassword($values['password']);
                $user->sendNewPasswordEmail();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'You have changed Your password'));
            }
        }
        $this->view->form = $form;
    }
    
    public function profileAction()
    {
        $request = $this->getRequest();
        $userId = $request->getParam('id');
        $user = $this->_em->getRepository('IAA\Entity\User')->find($userId);
        if (!$user) {
            throw new \Zend_Exception('No user found for public profile action, 
                ID given: '.$userId);
        }
        $latestComments = $this->_em->getRepository('IAA\Entity\Comment')
                ->findLatestCommentsForUser($user);
        $this->view->latestComments = $latestComments;
        $this->view->headTitle($user->getDisplayname() .' member profile');
        $this->view->member = $user;
    }

}
