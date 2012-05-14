<?php
/**
* 
*/
class Acl_IndexController extends \Zend_Controller_Action
{
	/**
	 * Displays all rules we have in systens for each module/controller/action and  it's roles
	 */
	public function indexAction()
	{
		
	}
	
	/**
	 * Display roles and it's users. 
	 * 
	 * Allows to add, delete or manage roles.
	 */
	public function rolesAction()
	{
        $repository = $this->_helper->getEm()->getRepository('\Trendmed\Entity\Role');
        $roles = $repository->findAll();
		if(!$roles) {
			throw new Exception("No roles in the database, that's akward. You should at least have guest role.", 500);
			
		}
		$this->view->roles = $roles;
		
		// Fetching form for new role
		$request = $this->getRequest();
		
		$form = new Acl_Form_Role();
		$roleId = $request->getParam('id');

		if(is_numeric($roleId) and $roleId > 0) {
			if(!$role = $repository->find($roleId)) {
				throw new Exception("No role id DB that have ID=".$roleId, 1);
			}
			$form->populate($role->toArray());
		}
		
		if($request->isPost()) {
			if($form->isValid($post = $request->getPost())) {
				
				if(!isset($role)) { // new role
					$role = new \Trendmed\Entity\Role;
				}
				
				$role->setName($post['name']);
				$this->_helper->getEm()->persist($role);
                $this->_helper->getEm()->flush();
				$this->_helper->FlashMessenger(array('success' => 'Changes to roles saved successfully'));
				$this->_helper->Redirector('roles', 'index', 'acl');
			} else { //form not valid
				$this->_helper->FlashMessenger(array('warning' => 'There where some errors saving to roles list, please fix them'));
				
			}
		}
		$this->view->form = $form;
		
	}
	
	public function deleteRoleAction() {
		$request = $this->getRequest();
		$roleId = $request->getParam('id');
		if(!is_numeric($roleId) and $roleId > 0) {
			throw new Exception('There is no role ID in the request, so what to delete', 500);
		}
		
		$roleTable = new Zend_Db_Table('aclrole');

		$role = $roleTable->find($roleId)->current();
		
		if(!$role) {
			throw new Exception("Could not find role ID: ".$roleId, 500);
		}
		$form = new Acl_Form_RoleDelete();
		
		if($request->isPost()) {
			if($form->isValid($request->getPost())) {
				$count = $role->delete();
				if($count == 1) { // should be one row deleted
					$this->_helper->FlashMessenger(array('success' => 'Role deleted'));
					$this->_helper->redirector('roles', 'index', 'acl');
				}
			}
		}
		
		$this->view->form = $form;
		
	}
	
	/**
	 * We can allow or deny access to any module/controller/action
	 */
	public function changePrivilegeAction()
	{
		
	}
}

?>