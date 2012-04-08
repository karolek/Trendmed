<?php
class Acl_IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }
    
    public function testThereIsTableOnIndexAction()
    {
        $this->dispatch('/acl/index');
        
        // assertions
        $this->assertModule('acl');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQuery("table#actions");
        
    }

    public function testAddNewRoleFormPresentOnRolesIndex()
    {
        $this->dispatch('/acl/index/roles');
        
        // assertions
        $this->assertModule('acl');
        $this->assertController('index');
        $this->assertAction('roles');
        $this->assertQuery("input#name");
    }

    public function testRoleGuestIsPresentOnRolesIndex()
    {
        $this->dispatch('/acl/index/roles');
        
        // assertions
        $this->assertModule('acl');
        $this->assertController('index');
        $this->assertAction('roles');
        $this->assertQueryContentContains("tbody > tr > td", 'guest', 'Musisz mieć role guest w systemie');
    }

	/**
	 * @dataProvider badRoleNames
	 */
	public function testPostingWrongValuesForRoleWillFail($name)
	{
		$this->request->setMethod('post')
			->setPost(array('name' => $name));
		$this->dispatch('/acl/index/roles');
		
		// assertions
		$this->assertModule('acl');
		$this->assertNotController('error');
		$this->assertQuery('div.alert-warning');
	}

	/**
	 * @dataProvider goodRoleNames
	 */	
	public function testPostingGoodValuesForRoleWillSucced($name)
	{
		$this->request->setMethod('post')
			->setPost(array('name' => $name));
		$this->dispatch('/acl/index/roles');
		
		// assertions
		$this->assertNotController('error');
		$this->assertRedirect();
	}
	
	public function testPostingAnIdAndNewNameOfRoleWillModifyIt()
	{
		$this->request->setMethod('get');
		$this->dispatch('/acl/index/roles/id/1');
		
		// assertions
		$this->assertNotController('error');
	}


	public function goodRoleNames() 
	{
		return array(
				array('name' => 'manager'),
				array('name' => 'tokem'),
				array('name' => '1')
			);
	}

	public function badRoleNames() 
	{
		return array(
				array('name' => ''),
				array('name' => "'OR''='"),
				array('name' => str_repeat('dupa', 1000))
			);
	}
}



