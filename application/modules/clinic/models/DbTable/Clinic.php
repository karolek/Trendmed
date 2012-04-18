<?php
class Clinic_Model_DbTable_Clinic extends Zend_Db_Table_Abstract
{
    protected $_name = 'clinic';
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
    
    public function findByToken($token)
    {
        $select = $this->select();
        $select->where('token = ?', $token)
            ->order('id DESC')
            ->limit(1);
        $row = $this->fetchRow($select);
        
        return $row;
        
    }
}

