<?php 
class Me_Controller_Action_Helper_GetEm extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     * Fetches Doctrine2 entity manager
     * @return type 
     */
    public function direct()
    {
        /* Fetching DB adapter */
        $resource = $this->getFrontController()
             ->getParam('bootstrap')
             ->getPluginResource('doctrine');
        $em = $resource->getEntityManager();
        return $em;
    }
}