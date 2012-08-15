<?php
/**
 * Reservation form
 */
class Catalog_Form_Reservation extends \Twitter_Form
{
    public function init()
    {
        $this->setName("reservation");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $services = new \Zend_Form_Element_MultiCheckbox('services');

        $submit      = new Zend_Form_Element_Submit('Make reservation');
        $submit->setLabel('Upload');

        $this->addElement($submit);
    }

    public function populateServicesFromClinic(\Trendmed\Entity\Clinic $clinic)
    {

    }
}