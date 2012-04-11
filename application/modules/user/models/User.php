<?php
/**
* 
*/
class User_Model_User extends Me_Model_Abstract implements Me_Model_Registerable_Interface
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_salt;
    protected $_role;
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }
    
    public function getSalt()
    {
      return $this->_salt;
    }

    public function setSalt($salt)
    {
      $this->_salt = $salt;
      return $this;
    }
    
    public function getRole()
    {
      return $this->_role;
    }

    public function setRole(Acl_Model_Role $role)
    {
      $this->_role = $role;
      return $this;
    }
    
    /**
     * Function provides authorization of a model in the sysatem.
     *
     * We check if param password == model password, if true than user is authorized.
     * Second param is for storeing the user in system for a period of time.
     */
    public function authorize($password, $rememberMe)
    {
	    $adapter = $this->_getAuthAdapter();
	    $adapter->setIdentity($this->getUsername())->setCredential($password);
	    $auth = Zend_Auth::getInstance();
	    $result = $auth->authenticate($adapter);
	    if($result->isValid()) {
	        $auth->getStorage()->write($this); // saveing userModel to session to use by
	        // Zend_Auth
	        return true;
	    } else {
	        return false;
	    }
    }
    
    protected function _getAuthAdapter()
	{
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $table = $this->getMapper()->getDbTable();
        
        $authAdapter->setTableName($table->getName())
            ->setIdentityColumn($table->getIdentityColumn())
            ->setCredentialColumn($table->getCredentialColumn());
            
        return $authAdapter;
	}
}
