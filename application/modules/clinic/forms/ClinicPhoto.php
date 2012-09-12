<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 07.05.12
 * Time: 22:05
 * To change this template use File | Settings | File Templates.
 */
class Clinic_Form_ClinicPhoto extends Twitter_Form
{
    protected $_photosUsed = 0;
    protected $_photosLeft = 0;

    public function init()
    {
        $this->setName("clinic_photo");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $this->setAttrib('enctype', 'multipart/form-data');

        $config = \Zend_Registry::get('config');

        for($i = 0; $i < $config->clinics->photo->limit; $i++ ) {
            $file = new Zend_Form_Element_File('photo'.$i);
            $file->setLabel('Photo file (jpg, png, gif)');
            // limit to 100K
            $file->addValidator('Size', false, 102400 * 10);
            // only JPEG, PNG, and GIFs
            $file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

            $this->addElement($file);
        }

        $submit      = new Zend_Form_Element_Submit('upload');
        $submit->setLabel('Upload');

        $this->addElement($submit);
    }

    public function setPhotosUsed($photosUsed)
    {
        # removing the file element
        $config = Zend_Registry::get('config');

        for ($i = 0; $i < $photosUsed; $i++) {
            if($this->getElement('photo'.$i)) {
                $this->removeElement('photo'.$i);
            }
        }
        $this->_photosLeft = $config->services->photo->limit - $photosUsed;
        $this->_photosUsed = $photosUsed;
    }

    public function getPhotosUsed()
    {
        return $this->_photosUsed;
    }

    public function getPhotosLeft()
    {
        return $this->_photosLeft;
    }
}