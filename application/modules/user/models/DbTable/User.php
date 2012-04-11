<?php
class User_Model_DbTable_User extends Br_Db_Table_Abstract
{
    protected $_name = 'acluser';
    protected $_identityColumn = 'email';
    protected $_credentialColumn = 'password';

    public function findByUsername($username)
    {
        $select = $this->select();
        $select->where('email = ?', $username)
            ->order('id DESC')
            ->limit(1);
        $row = $this->fetchRow($select);
        
        return $row;
        
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function getIdentityColumn()
    {
        return $this->_identityColumn;
    }
    
    public function getCredentialColumn()
    {
        return $this->_credentialColumn;
    }
}

