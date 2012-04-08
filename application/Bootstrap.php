<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initTwitterBootstrap() {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->headLink()->appendStylesheet('/css/bootstrap.css');
        $view->headLink()->appendStylesheet('/css/bootstrap-responsive.css');
        $view->headLink()->appendStylesheet('/css/datepicker.css');
        
        $view->headScript()->appendFile('/js/bootstrap.min.js', $type = 'text/javascript');
        $view->headScript()->appendFile('/js/bootstrap-datepicker.js', $type = 'text/javascript');   
    }
    
    /**
     * init jquery view helper, enable jquery, jqueryui, jquery ui css
     */
    protected function _initJquery() {
        $this->bootstrap('view');
        $view = $this->getResource('view'); //get the view object
        //add the jquery view helper path into your project
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        //jquery lib includes here (default loads from google CDN)
        $view->jQuery()->enable()->setVersion('1.7.2'); //jQuery version, automatically 1.5 = 1.5.latest
    }
}
