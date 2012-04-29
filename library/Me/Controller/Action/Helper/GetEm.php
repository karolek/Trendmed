<?php 
class Me_Controller_Action_Helper_GetEm extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     * Fetches Doctrine2 entity manager
     * @return type 
     */
    public function direct()
    {
        $resource = \Zend_Registry::get('doctrine');
        $em = $resource->getEntityManager();
        return $em;
    }
}