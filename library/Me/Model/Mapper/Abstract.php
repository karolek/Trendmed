<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mapper
 *
 * @author Bard
 */
abstract class Me_Model_Mapper_Abstract {
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
    
}

?>
