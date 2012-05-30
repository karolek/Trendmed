<?php

/**
 * Description of Service
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_Form_ServicePhoto extends Twitter_Form
{
    public function init()
    {
        $this->setName("service_photo");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $this->setAttrib('enctype', 'multipart/form-data');

        $file = new Zend_Form_Element_File('photo');
        $file->setLabel('Photo file (jpg, png, gif)');
        // ensure only 1 file
        $file->addValidator('Count', false, 1);
        // limit to 100K
        $file->addValidator('Size', false, 102400 * 10);
        // only JPEG, PNG, and GIFs
        $file->addValidator('Extension', false, 'jpg,png,gif');

        $this->addElement($file);

        $submit      = new Zend_Form_Element_Submit('upload');
        $submit->setLabel('Upload');

        $this->addElement($submit);
    }
}