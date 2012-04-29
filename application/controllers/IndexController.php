<?php

class IndexController extends \Zend_Controller_Action
{

    protected $_em;
    
    public function init()
    {
        $this->_em = $this->_helper->getEm();
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->view->headTitle('Homepage');
        $this->_helper->layout()->setLayout('homepage');
    }


}

