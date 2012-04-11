<?php 
class Me_Controller_Action_Helper_GetDb extends Zend_Controller_Action_Helper_Abstract {
    
    public function direct()
    {
        /* Fetching DB adapter */
        $resource = $this->getFrontController()
             ->getParam('bootstrap')
             ->getPluginResource('db');
        $db = $resource->getDbAdapter();
        return $db;
    }
}