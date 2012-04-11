<?php 
/**
* 
*/
class Acl_Model_RoleMapper extends Me_Model_Mapper_Abstract
{
    protected $_dbTable = 'Acl_Model_DbTable_Aclrole';
    
    public function findByName($name)
    {
        $select     = $this->getDbTable()->select();
        $select->where('name = ?', $name);
        $row     = $this->getDbTable()->fetchRow($select);
        if(!$row) return;
    
        $entry = new Acl_Model_Role($row->toArray());
        return $entry;
    }
    
    public function find($id)
    {
        $row     = $this->getDbTable()->find($id)->current();
        if(!$row) return false;
        $entry = new Acl_Model_Role($row->toArray());
        return $entry;
    }
}
