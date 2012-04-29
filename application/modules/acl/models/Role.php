<?php 
use Me\Model;
/**
* 
*/
class Acl_Model_Role extends Me_Model_Abstract
{
    protected $_id;
    protected $_name;
    
    public function getId()
    {
      return $this->_id;
    }

    public function setId($id)
    {
      $this->_id = $id;
      return $this;
    }
    
    public function getName()
    {
      return $this->_name;
    }

    public function setName($name)
    {
      $this->_name = $name;
      return $this;
    }
}
