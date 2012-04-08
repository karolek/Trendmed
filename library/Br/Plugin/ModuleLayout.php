<?php
/**
 * Plugin for initialization seperate layout for each module.
 * 
 * Plugin checks for existance of a layout file inside the defaul layput directory,
 * (not in module directory!). 
 * If the file exists, than the layout is set to the file.
 *
 * @author Bartosz Rychlicki
 * @package Br
 */
class Br_Plugin_ModuleLayout extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

        $module = $request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();

        // check module and automatically set layout
        $layoutsDir = $layout->getLayoutPath();
        // check if module layout exists else use default
        if(file_exists($layoutsDir . DIRECTORY_SEPARATOR . $module . ".phtml")) {
            $layout->setLayout($module);
        } else {
            $layout->setLayout("layout");
        }
	}
}
