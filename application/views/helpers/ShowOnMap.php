<?php
class Trendmed_View_Helper_ShowOnMap extends Zend_View_Helper_Abstract
{
    public $view;
    protected $_rootNode;
    protected $_apiKey = 'AIzaSyATnHOlCSbWDBeKD7cfgUFa9GtAg_6fRUM';

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function ShowOnMap(\Trendmed\Entity\Clinic $entity, $width = 300, $height = 200, $zoomLevel = 8)
    {
        $address = urlencode(
            $entity->getStreetaddress(). ', '. $entity->getPostcode().' '.
                $entity->getCity().', '.$entity->getProvince(). ', Poland');
        $src = 'http://maps.googleapis.com/maps/api/staticmap?zoom='.$zoomLevel.'&sensor=false&size='. $width .'x'. $height.
            '&markers=color:blue%7C'.$address;
        return $src;
    }

}