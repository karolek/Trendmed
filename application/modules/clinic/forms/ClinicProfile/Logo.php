<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 07.05.12
 * Time: 22:05
 * To change this template use File | Settings | File Templates.
 */
class Clinic_Form_ClinicProfile_Logo extends Twitter_Form
{
    public function init()
    {
        $this->setName("change_logo");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $this->setAttrib('enctype', 'multipart/form-data');

        $file = new Zend_Form_Element_File('logo');
        $file->setLabel('Logo file (jpg, png, gif)');
        // ensure only 1 file
        $file->addValidator('Count', false, 1);
        // limit to 100K
        $file->addValidator('Size', false, 102400*10);
        // only JPEG, PNG, and GIFs
        $file->addValidator('Extension', false, 'jpg,png,gif');

        $this->addElement($file);

        $submit      = new Zend_Form_Element_Submit('signin');
        $submit->setLabel('Upload');

        $this->addElement($submit);
    }
}