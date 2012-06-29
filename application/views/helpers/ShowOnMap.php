<?php
class Trendmed_View_Helper_ShowOnMap extends Zend_View_Helper_Abstract
{
    public $view;
    protected $_rootNode;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function ShowOnMap(\Trendmed\Entity\Clinic $entity)
    {
        // TODO: implement JavaScript to show entity on Google Maps
        $output = "<a>" . $this->view->translate('Show on map'). "</a>";
        return $output;
    }

}