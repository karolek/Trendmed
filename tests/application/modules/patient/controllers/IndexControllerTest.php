<?php
class Patient_IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testLoginFormIsOnIndex()
    {
        $this->dispatch('/patient');
        
        // assertions
        $this->assertModule('patient');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery("form#login");
    }
    
    public function testPassingEmptyForWillNotGetMeIn()
    {
		$this->request->setMethod('post')
						->setPost(array(
							'username' => '',
							'password'	=> '',
							));
	
        $this->dispatch('/patient');
        
        // assertions
        $this->assertModule('patient');
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
	
        $this->dispatch('/patient');
        
        // assertions
        $this->assertNotRedirectTo('/');
        $this->assertModule('patient');
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



