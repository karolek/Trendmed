<?php

class User_RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

	/**
	 * This action is resonsible for regestring new user in the system. 
	 *
	 * Action use any model that implemenets << Br_Model_Interface_Registerable >> interface.
	 */
    public function indexAction()
    {
        //1. We need to display form for user registration. Remeber that model also should
		// have validation
		$request 	= $this->getRequest();
		$userTable	= new User_Model_DbTable_User();
		
		$form 		= $this->getRegistrationForm($userTable->createRow());
		
		if($request->isPost()) {
			$post = $request->getPost();
			if($form->isValid($post)) { // data in the form are valid so we can register new user
					$username = $post['username'];
					$password = $post['password'];
					$userTable->registerNewUser($username, $password, $post); //registration 
			}
		}
		
		$this->view->form = $form;
    }

	public function getRegistrationForm($rowModel)
	{
		return new User_Form_Registration($rowModel);
	}

}

