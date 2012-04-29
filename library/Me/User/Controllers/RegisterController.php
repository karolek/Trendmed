<?php
/**
 * The idea of this class is to provice simple controller for every 
 * registration proccess for every user You would even need.
 * 
 * You need to suply vaues for two variables $_roleName with will be the name
 * from aclrole (I will register new user with that role) and a $_userMode class
 * name. This object will be saved using the mapper.  
 */
abstract class Me_User_Controllers_RegisterController extends Zend_Controller_Action
{
    protected $_userModel; // class name of the user model
    protected $_redirectAfterRegistration = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default',
    );
    protected $_messageSuccessAfterRegistration = array(
      'success' => 'You have registered succesfuly. You can login now.'
    );

    public function init()
    {
        /* Initialize action controller here */
    }

	/**
	 * This action is resonsible for regestring new user in the system. 
	 *
	 */
    public function indexAction()
    {
        $request 	= $this->getRequest();
		$form 		= $this->getRegistrationForm();
        $em         = $this->_helper->getEm();
                
        // we initialize model (you should fill _userModel in Your controller)
		$model = new $this->_userModel;

		if($request->isPost()) {
			$post = $request->getPost();
			if($form->isValid($post)) { // data in the form are valid so we 
            // can register new user
                $values = $form->getValues();
			    $model->setOptions($values);
                $model->sendWelcomeEmail();
   			    
                $em->persist($model);

			    $this->_helper->FlashMessenger($this->_messageSuccessAfterRegistration);
			    $this->_helper->Redirector(
                        $this->_redirectAfterRegistration['action'],
                        $this->_redirectAfterRegistration['controller'],
                        $this->_redirectAfterRegistration['module']
                        );
			} else {
			    $this->_helper->FlashMessenger(array('error' => 'Please fill out the form correctly'));
			}
		}
		
        $this->view->headTitle(ucfirst($model->getRoleName()).' registration');
		$this->view->form = $form;
    }

    /**
     * To implement in extending controller. 
     */
	public function getRegistrationForm()
	{

	}

}

