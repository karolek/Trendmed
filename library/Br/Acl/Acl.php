<?php
class Br_Acl_Acl extends Zend_Acl {
	public function isAllowed($identity, $request, $privilige)
	{
		if(!isset($identity)) {
			$userId = 1;
		} else {
			$userId = $identity;
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select(array('module_name', 'controller_name', 'action_name'))
			->from('aclprivilege')
			->join('aclrole', 'aclrole.id = aclprivilege.role_id')
			->join('acluser_aclrole', 'acluser_aclrole.role_id = aclrole.id')
			->where('acluser_aclrole.user_id = ?', $userId)
			->where('module = "?" OR module = "%"', $request->getModuleName())
			->where('controller = "?" OR controller = "%"', $request->getControllerName())
			->where('action = "?" OR action = "%"', $request->getActionName());
		
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();
		if(count($result) == 0) { 
			return false;
		} else {
			return true;
		}
	}

	public function isRoleAllowed($roleId, $request, $privilige)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select(array('module_name', 'controller_name', 'action_name'))
			->from('aclprivilege')
			->where('role_id = ?', $roleId)
			->where('module = ? OR module = "%"', $request->getModuleName())
			->where('controller = ? OR controller = "%"', $request->getControllerName())
			->where('action = ? OR action = "%"', $request->getActionName());
//			->join('aclrole', 'aclrole.id = aclprivilege.role_id');
//			->join('acluser', 'acluser.aclrole_id = aclrole.id');
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();
		if(count($result) == 0) { 
			return false;
		} else {
			return true;
		}
	}
}