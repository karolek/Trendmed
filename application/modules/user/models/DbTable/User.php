<?php
class User_Model_DbTable_User extends Br_Db_Table_Abstract
{
    protected $_name = 'acluser';

    public function findByUsername($username)
    {
        $select = $this->select();
        $select->where('email = ?', $username)
            ->order('id DESC')
            ->limit(1);
        $row = $this->fetchRow($select);
        
        return $row;
        
    }
}

