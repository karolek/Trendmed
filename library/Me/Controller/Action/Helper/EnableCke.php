<?php 
class Me_Controller_Action_Helper_EnableCke extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var Zend_View_Interface
     */
    public $view;


    /**
     * Adds JavaScript for the CKEditor
     * @param $view Zend_View_Interface
     * @param $elements Array of elements (names and ids) to initialize ckeditor on
     * @param $mode String toolbar type
     *
     * @return type 
     */
    public function direct(Zend_View_Interface $view, $elements, $mode = "Basic", $options = array())
    {
        if(!$this->view) {
            $this->view = $view;
        }
        $this->view->headScript()->prependFile('/ckeditor/adapters/jquery.js');
            $this->view->headScript()->prependFile('/ckeditor/ckeditor.js');
            $this->view->headScript()->prependFile('/ckfinder/ckfinder.js');

        $opt = "";
        foreach($options as $key => $value) {
            $opt .= "$key : '$value'\n";
        }

        foreach($elements as $element) {
            $this->view->headScript()->appendScript("
            $(document).ready(function() {
                $('#$element').ckeditor( function() { /* callback code */ }, {
                    toolbar: '$mode'
                    $opt
                });
                var editor = $('#$element').ckeditorGet();
                CKFinder.setupCKEditor( editor, '/ckfinder/' );
            });"

            );
        }
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