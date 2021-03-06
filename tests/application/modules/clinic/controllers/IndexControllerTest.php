<?php
class Clinic_IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }
    
    // sprawdzamy czy zapamientywanie w sesji dziala
    public function testIfRememberMeWorks()
    {
    	$this->dispatch('/clinic');
    	
    }

    public function testLoginFormIsOnIndex()
    {
        $this->dispatch('/clinic');
        
        // assertions
        $this->assertModule('clinic');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery("form#login");
    }
    
    public function testlogoutActionIsNotErrorPage()
    {
        $this->dispatch('/clinic/index/logout');
        
        $this->assertNotController('error');
    }
    
    public function testPasswordRecoveryActionDisplaysForm()
    {
        $this->dispatch('/clinic/index/password-recovery');
        $this->assertQuery("form#passwordRecovery");
    }        
    
    public function testWrongEmailForPasswordRecoveryWillReturnError()
    {
        $this->request->setMethod('post')
                ->setPost(array(
                   'username' => 'aajajkajaajkajkjk@wp.pl' // does not exists 
                ));
        $this->dispatch('/clinic/index/password-recovery');
        $this->assertNotController('error');
        $this->assertAction('password-recovery');
        $this->assertQueryContentContains('div.alert', 'No such user');
    }
    
    public function testIfRecoveryActionWithoutTokenWillNotwork()
    {
        $this->dispatch('/clinic/index/new-password-from-token');
        
        $this->assertController('error');
        $this->assertAction('error');
    }
    
    public function testPassingEmptyForWillNotGetMeIn()
    {
		$this->request->setMethod('post')
						->setPost(array(
							'username' => '',
							'password'	=> '',
							));
	
        $this->dispatch('/clinic');
        
        // assertions
        $this->assertModule('clinic');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery("form");
    }

	/**
	 * @dataProvider wrongUserNameDataProvider
	 */
	public function testPassingWrongUserNameWillNotGetMeIn($username, $password)
	{
		$this->request->setMethod('post')
						->setPost(array(
							'username' => $username,
							'password'	=> $password,
							));
	
        $this->dispatch('/clinic');
        
        // assertions
        $this->assertNotRedirectTo('/');
        $this->assertModule('clinic');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery("form");
	}
	
	public function wrongUserNameDataProvider() {
		return array(
			array('username' => 'niematakiegousera', 'password' => 'krysp'),
			array('username' => '', 'password' => ''),
			array('username' => rand(1,100000), 'password' => rand(1, 100000000)),
			array('username' => 'niematakiegousera', 'password' => 'krysp'),
		);
	}
}



