<?php
/**
 * Controller for public (displayed to non clinic users) information's and actions
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_PublicController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();

    }

    public function profileAction()
    {

    }

}
