<?php
/**
 * Just to get the config object in view
 *
 * @package Br
 * @author Bartosz Rychlicki <b@br-design.pl>
 */
class Trendmed_View_Helper_GetConfig extends Zend_View_Helper_Abstract
{
    public function GetConfig()
    {
        if(Zend_Registry::isRegistered('config')) {
            return Zend_Registry::get('config');
        } else {
            return null;
        }
    }
}
