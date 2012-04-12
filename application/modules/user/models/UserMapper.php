<?php
/**
* 
*/
class User_Model_UserMapper extends Me_Model_Mapper_Abstract
{
    protected $_dbTable = 'User_Model_DbTable_User';

    public function save(User_Model_User $model)
    {
        $data = array(
            'email'             => $model->getUsername(),
            'password'          => $model->getPassword(),
            'aclrole_id'        => $model->getRole()->id,
            'token'             => $model->getToken(),
            'tokenvaliduntil'   => $model->getTokenValidUntil(),
            'created'           => time(),
        );
 
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    protected function _createNewModelFromRow($row)
    {
       $model = new User_Model_User;
       $model->id       = $row->id;
       $model->username = $row->email;
       $model->password = $row->password;
       $model->token    = $row->token;
       $model->tokenValidUntil = $row->tokenvaliduntil;
       // fetching role
       $roleMapper = new Acl_Model_RoleMapper();
       $role = $roleMapper->find($row->aclrole_id);
       if(!$role) throw new Exception("Didnt found role for user", 500);
       
       $model->role = $role;

       return $model;
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
}