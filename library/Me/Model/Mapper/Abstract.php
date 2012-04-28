<?php
namespace Me;
abstract class Mapper {
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (!$this->_dbTable instanceof Zend_Db_Table_Abstract) {
            $this->_dbTable = new $this->_dbTable;
        } 
        return $this->_dbTable;
    }
    
    public function delete($model)
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
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = $this->_createNewModelFromRow($row);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    /**
     * Implement in Your model. Creates a new model based on Zend_Db_Table_Row Object.
     */
    protected function _createNewModelFromRow($row)
    {
        
    }
}
