<?php
/**
 * @author Bartosz Rychlicki <b@br-design.pl>
 */
abstract class Br_Controller_Action extends Zend_Controller_Action {

    protected $_log             = null;
    protected $_config          = null;
    protected $_isAjax          = null;
    protected $_db				= null;
	protected $_logger			= null;

    public function init() {

        $request = $this->getRequest();
        $this->_isAjax          = $this->_request->isXmlHttpRequest();
        
		/* Add Zend_Log */
		$loggerResource = $this->getFrontController()
							->getParam('bootstrap')
							->getPluginResource('log');
		$this->_logger = $loggerResource->getLog();
		
        /* Fetching DB adapter */
        $resource = $this->getFrontController()
            ->getParam('bootstrap')
            ->getPluginResource('db');
		$this->_db = $resource->getDbAdapter();
		
    }
}
