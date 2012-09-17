<?php
use \Trendmed\Entity\BannerAd;

/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 17.05.12
 * Time: 11:27
 * To change this template use File | Settings | File Templates.
 */
class Admin_Form_BannerAd extends Twitter_Form
{

    protected $_zones = array();

    public function init()
    {
        $this->setName('banner_ad');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $file = new \Zend_Form_Element_File('file');
        $file->setRequired(true);
        $file->setLabel('Your banner file');
        $file->setDescription('Supply file according to type of a banner');
        $this->addElement($file);

        $type = new \Zend_Form_Element_Select('type');
        // adding banner types
        $type->addMultiOption(0, '-- CHOSE --');
        foreach (BannerAd::$BANNER_TYPES as $reference => $label) {
            $type->addMultiOption($reference, $label);
        }
        $type->setRequired(true);
        $type->setLabel('Choose the type of a banner');
        $this->addElement($type);

        $description = new \Zend_Form_Element_Text('description');
        $description->addFilter('StripTags');
        $description->setLabel('Brief description');
        $description->setRequired(true);
        $description->setDescription('This is only for your information');
        $description->setLabel('Ad description');
        $this->addElement($description);

        $zone = new \Zend_Form_Element_Select('zone');
        $zone->addMultiOption(0, '-- CHOSE --');
        $zone->setLabel('Where to shown this banner');
        $zone->setDescription('You can choose the zone on site in with this banner will rotate');
        $this->addElement($zone);

        $link = new \Zend_Form_Element_Text('target');
        $link->setLabel('Target URL');
        $link->setDescription('You can leave this empty, then banner will be not clickable');
        $link->setOptions(
            array(
                'filters'    => array(
                    'StringTrim',
                    'StripTags',
                ),
                'validators' => array(
                    array(
                        'Callback',
                        true,
                        array(
                            'callback' => function($value) {
                                return Zend_Uri::check($value);
                            }
                        ),
                        'messages' => array(
                            Zend_Validate_Callback::INVALID_VALUE => 'Please enter a valid URL',
                        ),
                    ),
                ),
            )
        );
        $this->addElement($link);

        $target = new \Zend_Form_Element_Select('openIn');
        foreach (BannerAd::$LINK_TARGETS as $reference => $label) {
            $target->addMultiOption($reference, $label);
        }
        $target->setRequired(true);
        $target->setLabel('Where to open this link');

        $this->addElement($target);

        $active = new \Zend_Form_Element_Checkbox('isActive');
        $active->setLabel('Publish this ad');
        $this->addElement($active);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Upload');
        $this->addElement($submit);
    }

    protected function _populateZones($zones, $element) {
        foreach($zones as $key => $value) {
            $element->addMultiOption($key, $value['name']);
        }
        return $element;
    }

    public function setZones($zones)
    {
        $this->_zones = $zones;
        $this->_populateZones($this->_zones, $this->getElement('zone'));
    }

    public function getZones()
    {
        return $this->_zones;
    }


}
