<?php
class User_Model_DbTable_User extends Br_Db_Table_Abstract
{
    protected $_name = 'acluser';
	protected $_rowClass = 'User_Model_DbTable_Row_User'; // You can supply any Zend_Db_Table_Row object that implemenets User_Model_User_Interface for a userclass
	
	/**
	 * This function save an user object (any Zend_Db_Table_Row Object passed
	 * into $_rowClass param into datbase.
	 *
	 * @param String $username Username (could be any string)
	 * @param String $password User plain password (not hashed)
	 * @param Array $data All other data that You want to save with user
	 */
	public function registerNewUser($username, $password, $data = array())
	{
		$newUser = $this->createRow(); // creating new user object from $_rowClass/
		if(!$newUser instanceof User_Model_User_Interface) {
			throw new Exception("Supplied user row class should implemenet User_Model
			_User_Interface, and it's not", 500);
		}
		$newUser->setFromArray($data); // we'r posting any extra data to new user object
		$newUser->setUsername($username);
		$newUser->setPassword($password);
		
		if(!$newUser->save()) {
			throw new Exception("New user save() failed. Here are the messages: ".$newUser->getMessages(), 500);
		}
		return $newUser;
	}
}

