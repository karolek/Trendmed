<?php
/**
* This helper will prepare a link based on info if user is logged or not.
* If the user is not logged than the link will be prepared to direct user to login form (on modal)
*
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Trendmed_View_Helper_LoggedLink extends Zend_View_Helper_Abstract {

    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function scriptPath($script) {
        return $this->view->getScriptPath($script);
    }

    public function LoggedLink($content, $attributes = null) {
        if(is_array($attributes)) {
            $attrs = array();
            foreach($attributes as $name => $value) {
                $attrs[] = $name.'="'.$value.'"';
            }
            // checking if user is logged
            if(!$this->view->LoggedUser()) {
                $attrs[] = 'data-toggle = "modal"';
                $attrs[] = 'data-target = "#loginCallToAction"';
            }
            $readyAttributes = \implode(' ', $attrs);
        } else {
            $readyAttributes = null;
        }

        $output = '<a href="#" '.$readyAttributes.'>'.$content.'</a>';
        // if we are using this function then we are saveing user URL to redirect after login in session
        $session = new \Zend_Session_Namespace('login_redirect');
        $session->url = $_SERVER['REQUEST_URI'];

        return $output;
    }

}