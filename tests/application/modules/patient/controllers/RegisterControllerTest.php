<?php

class Patient_RegisterControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $params = array(
            'action'        => 'index', 
            'controller'    => 'register', 
            'module'        => 'patient'
        );
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQuery("form#register");
        $this->assertQuery('form#register input[name="password_confirmation"]');
    }
    
    public function testEmptyRegistrationFormWillReturnValidationErrors()
    {
        $this->request->setMethod('post')
                ->setPost(array(
                    'username' => '',
                    'password' => '',
                    'password_confirmation' => '',
                ));

        $params = array(
            'action' => 'index',
            'controller' => 'register',
            'module' => 'patient'
        );
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        $this->assertQuery("form#register");
        $this->assertQuery('form#register input[name="password_confirmation"]');
        $this->assertQueryContentContains('div.alert', 'Please fill out the form correctly');
    }

}



