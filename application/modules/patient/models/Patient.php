<?php
class Patient_Model_Patient extends Me_User_Model_User_Abstract
implements Me_User_Model_User_Interface {
    protected $_id;
    protected $_user;
    protected $_realname;
    protected $_phone;
    protected $_country;
    protected $_address;
    
    public function __construct() {
        if(!$this->getRole()) {
            $roleMapper = new Acl_Model_RoleMapper();
            $role = $roleMapper->findByName('patient');
            $this->setRole($role);
        }
    }

}
?>
