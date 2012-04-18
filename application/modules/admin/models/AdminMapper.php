<?php
class Admin_Model_AdminMapper extends Me_Model_Mapper_Abstract
implements Me_User_Model_User_Mapper_Interface
{
    protected $_dbTable = 'Admin_Model_DbTable_Admin';

    public function save(Admin_Model_Admin $model)
    {
        $data = array(
            'name'              => $model->getName(),
            'street'            => $model->getStreet(),
            'city'              => $model->getCity(),
            'province'          => $model->getProvince(),
            'postcode'          => $model->getPostcode(),
            'representantname'  => $model->getRepresentantname(),
            'representantphone' => $model->getRepresentantphone(),
            'email'             => $model->getEmail(),
            'password'          => $model->getPassword(),
            'salt'              => $model->getSalt(),
            'aclrole_id'        => $model->getRole()->id,
            'token'             => $model->getToken(),
            'tokenvaliduntil'   => $model->getTokenValidUntil(),
        );
 
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $data['created']  = time();
            $id = $this->getDbTable()->insert($data);
        } else {
            $data['modified'] = time();
           $id = $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        $model->id = $id;
        return $id;
    }
    
    public function findByUsername($username)
    {
        $row = $this->getDbTable()->findByUsername($username);
        if(!$row) return;
        $entry = $this->_createNewModelFromRow($row);
        return $entry;    
    }
    
    public function findByToken($token)
    {
        $row = $this->getDbTable()->findByToken($token);
        if(!$row) return;
        $entry = $this->_createNewModelFromRow($row);
        return $entry;    
    }
    
    public function _createNewModelFromRow($row)
    {
        $model = new Admin_Model_Admin();
        $model->id          = $row->id;
        $model->email       = $row->email;
        $model->password    = $row->password;
        $model->created     = $row->created;
        $model->token       = $row->token;
        $model->tokenValidUntil = $row->tokenvaliduntil;
        $model->username    = $row->username;
        return $model;
    }
}

