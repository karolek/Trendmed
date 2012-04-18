<?php
class Admin_Model_Admin extends Me_User_Model_User_Abstract
implements Me_User_Model_User_Interface
{
    protected $_id;
    protected $_username;
    protected $_email;
    protected $_password;
    protected $_role;
    protected $_created;
    protected $_token;
    protected $_tokenValidUntil;
    protected $_lastLoginTime;
    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setUsername($username) {
        $this->_username = $username;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setPassword($password) {
        $this->_password = $password;
    }

    public function getRole() {
        return $this->_role;
    }

    public function setRole(Acl_Model_Role $role) {
        $this->_role = $role;
    }

    public function getCreated() {
        return $this->_created;
    }

    public function setCreated($created) {
        $this->_created = $created;
    }

    public function getToken() {
        return $this->_token;
    }

    public function setToken($token) {
        $this->_token = $token;
    }

    public function getTokenValidUntil() {
        return $this->_tokenValidUntil;
    }

    public function setTokenValidUntil($tokenValidUntil) {
        $this->_tokenValidUntil = $tokenValidUntil;
    }

    public function getLastLoginTime() {
        return $this->_lastLoginTime;
    }

    public function setLastLoginTime($lastLoginTime) {
        $this->_lastLoginTime = $lastLoginTime;
    }

    
    public function __construct() {
        if(!$this->getRole()) {
            $roleMapper = new Acl_Model_RoleMapper();
            $role = $roleMapper->findByName('admin');
            $this->setRole($role);
        }
    }
}