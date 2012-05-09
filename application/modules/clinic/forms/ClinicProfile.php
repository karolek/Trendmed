<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 07.05.12
 * Time: 22:02
 *
 * Add specific subforms for ClinicProfile forms directory
 */
class Clinic_Form_ClinicProfile extends Twitter_Form
{
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setAttrib('class', 'form-horizontal');

        $this->addSubForm(new Clinic_Form_ClinicProfile_Logo(), 'logo');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');

        $this->addElement($submit);
    }
}
