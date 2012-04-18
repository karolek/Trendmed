<?php
class Clinic_Model_Clinic extends Me_User_Model_User_Abstract
implements Me_User_Model_User_Interface
{
    protected $_id;
    protected $_name;
    protected $_email;
    protected $_street;
    protected $_city;
    protected $_postcode;
    protected $_province;
    protected $_representantname;
    protected $_representantphone;

    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getEmail() {
        return $this->_email;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function getStreet() {
        return $this->_street;
    }

    public function setStreet($street) {
        $this->_street = $street;
    }

    public function getCity() {
        return $this->_city;
    }

    public function setCity($city) {
        $this->_city = $city;
    }

    public function getPostcode() {
        return $this->_postcode;
    }

    public function setPostcode($postcode) {
        $this->_postcode = $postcode;
    }

    public function getProvince() {
        return $this->_province;
    }

    public function setProvince($province) {
        $this->_province = $province;
    }

    public function getRepresentantname() {
        return $this->_representantname;
    }

    public function setRepresentantname($representantname) {
        $this->_representantname = $representantname;
    }

    public function getRepresentantphone() {
        return $this->_representantphone;
    }

    public function setRepresentantphone($representantphone) {
        $this->_representantphone = $representantphone;
    }
    
    public function getEmailaddress()
    {
        return $this->_email;
    }

    public function __construct() {
        if(!$this->getRole()) {
            $roleMapper = new Acl_Model_RoleMapper();
            $role = $roleMapper->findByName('clinic');
            $this->setRole($role);
        }
    }
}