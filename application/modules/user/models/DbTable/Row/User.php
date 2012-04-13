<?php
/**
* 
*/
class User_Model_DbTable_Row_User extends Br_Db_Table_Row_Abstract implements User_Model_DbTable_Row_Interface_User
{
	protected $_validators = array(
		'username' => array(
			'EmailAddress', array(),
			),
	);
	
	public function setUsername($username) 
	{
		$this->email = $username;
		return $this;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	
	public function setSalt($salt)
	{
		$this->salt = $salt;
		return $this;
	}
	
	public function setPassword($password)
	{
		$salt = $this->_generateSalt();
		$this->setSalt($salt);
		
		$password = sha1($password . $salt);
		$this->password = $password;
		return $this;
	}
	
	protected function _generateSalt()
	{
		return md5(substr(str_repeat('loremipsumsitdoloramter', rand(20,40)), rand(20,200), rand(201, 1000))) . time();
	}
}
