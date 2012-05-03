<?php 
class Me_Controller_Action_Helper_GetLogger extends Zend_Controller_Action_Helper_Abstract {
    
    public function direct()
    {
        /* Fetching Logger */
        $resource = $this->getFrontController()
             ->getParam('bootstrap')
             ->getResource('log');
        return $resource;
    }
}