<?php

/**
 * Description of Service
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Service extends Twitter_Form
{
    public function init()
    {
        $this->setMethod('post');
        $name = new \Zend_Form_Element_Text('name');
        $name->setRequired(true);
        $name->addFilter(new Zend_Filter_StripTags());
    }
}