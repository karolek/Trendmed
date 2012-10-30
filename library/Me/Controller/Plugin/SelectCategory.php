<?php
/**
 * checks if there is any selected category slug in the session, if yes, passes it to the view
 */
class Me_Controller_Plugin_SelectCategory extends Zend_Controller_Plugin_Abstract
{

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $session = new Zend_Session_Namespace('visitedCategory');
        if($session->slug != '') {
            $view = Zend_Controller_Front::getInstance()
                ->getParam('bootstrap')
                ->getResource('view');

            $view->selectedCategory = $session->slug;
        }

	}
}
