<?php
/**
* 
*/
class User_Model_User extends Me_Model_Abstract
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
}
