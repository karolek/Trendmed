<?php
class Catalog_Model_DbTable_Category extends Zend_Db_Table_Abstract 
{
    protected $_name = 'category';
    protected $_referenceMap = array(
        'category' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Catalog_Model_DbTable_Category',
            'refColumns' => 'id'
        ),
    );

}