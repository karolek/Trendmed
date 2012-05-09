<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 07.05.12
 * Time: 22:40
 * To change this template use File | Settings | File Templates.
 */
class  Clinic_Form_ClinicProfile_Basic extends Twitter_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name of the clinic');
        $name->setRequired(true);
        $this->addElement($name);

        $type = new Zend_Form_Element_Select('type');
        $config = Zend_Registry::get('config');
        $translate = Zend_Registry::get('Zend_Translate');
        $type->setLabel('Type of Clinic');
        foreach ($config->clinics->types as $key => $value) {
            $type->addMultiOption($key, $translate->_($value));
        }
        $this->addElement($type);
    }
}
