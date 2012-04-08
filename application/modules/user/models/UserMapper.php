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
    
    public function delete(Application_Model_Expense $model)
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
 
    public function fetchAll()
    {
        $select    = $this->getDbTable()->select();
        $select->order('date DESC');
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = $this->_createNewModelFromRow($row);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function fetchAllForMonth($timestamp)
    {
        $select     = $this->getDbTable()->select();
        $month      = date("m", $timestamp);
        $year       = date("Y", $timestamp);
        
        $select->where('date > ?', mktime(0, 0, 0, $month, 1, $year))->order('date DESC');
        
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = $this->_createNewModelFromRow($row);
            $entries[] = $entry;
        
        }
        return $entries;
    }    
    
    protected function _createNewModelFromRow(Zend_Db_Table_Row $row)
    {
        $entry = new Application_Model_Expense($row->toArray());
        $category = $row->findParentApplication_Model_DbTable_Category();
        if($category) {
            $categoryModel = new Application_Model_Category($category->toArray());
            $entry->setCategory($categoryModel);
        }
        return $entry;
    }
}