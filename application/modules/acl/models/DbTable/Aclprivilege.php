<?php
class Acl_Model_DbTable_Aclprivilege extends Br_Db_Table_Abstract
{
    protected $_referenceMap    = array(
            'Role' => array(
                'columns'           => 'role_id',
                'refTableClass'     => 'aclrole',
                'refColumns'        => 'id'
            ),
        );
        
    protected $_name = 'aclprivilege';
    
    public function getAllPrivileges($order = 'role_id')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $select = $db->select();
        $select->from($this->_name)
            ->order($order)
            ->join('aclrole', 'aclrole.id = aclprivilege.role_id');
        return $db->fetchAll($select);
    }
}

