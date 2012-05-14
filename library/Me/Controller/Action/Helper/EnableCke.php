<?php 
class Me_Controller_Action_Helper_EnableCke extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var Zend_View_Interface
     */
    public $view;


    /**
     * Adds JavaScript for the CKEditor
     * @return type 
     */
    public function direct(Zend_View_Interface $view, $mode = "basic")
    {
        if(!$this->view) {
            $this->view = $view;
        }
        $this->view->headScript()->prependFile('/ckeditor/adapters/jquery.js');
        $this->view->headScript()->prependFile('/ckeditor/ckeditor.js');
        $this->view->headScript()->appendScript("
        ");
    }

    /**
     * Set the view object
     *
     * @param  Zend_View_Interface $view
     * @return Zend_Controller_Action_Helper_ViewRenderer Provides a fluent interface
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }
}