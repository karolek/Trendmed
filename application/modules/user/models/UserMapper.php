<?php
/**
* 
*/
class User_Model_UserMapper extends Me_Model_Mapper_Abstract
{
    protected $_dbTable = 'User_Model_DbTable_User';

    public function save(User_Model_User $model, Acl_Model_Role $role)
    {
        $data = array(
            'email'         => $model->getUsername(),
            'password'      => $model->getPassword(),
            'aclrole_id'    => $role->id,
            'created'       => time(),
        );
 
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function delete(User_Model_User $model)
    {
        $this->getDbTable()->delete(array('id = ?' => $model->getId()));
    }
     
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $entry = $this->_createNewModelFromRow($row);
        return $entry;
    }
    
    public function findByUsername($username)
    {
        $row = $this->getDbTable()->findByUsername($username);
        if(!$row) return;
        $entry = $this->_createNewModelFromRow($row);
        return $entry;    
    }
 
    public function fetchAll()
    {
        $select    = $this->getDbTable()->select();
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = $this->_createNewModelFromRow($row);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    protected function _createNewModelFromRow($row)
    {
       $model = new User_Model_User;
       $model->id       = $row->id;
       $model->username = $row->email;
       $model->password = $row->password;
       // fetching role
       $roleMapper = new Acl_Model_RoleMapper();
       $role = $roleMapper->find($row->aclrole_id);
       if(!$role) throw new Exception("Didnt found role for user", 500);
       
       $model->role = $role;

       return $model;
    }
    
}