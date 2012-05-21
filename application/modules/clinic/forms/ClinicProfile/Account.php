<?php
/**
 * Description of ClinicDetails
 *
 * @author Bard
 */
class Clinic_Form_ClinicProfile_Account extends Twitter_Form
{
    public function init()
    {
        $this->setName("account");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $type = new Zend_Form_Element_Select('type');
        $config = Zend_Registry::get('config');
        $translate = Zend_Registry::get('Zend_Translate');
        $type->setLabel('Type of Clinic');
        foreach ($config->clinics->types as $key => $value) {
            $type->addMultiOption($key, $translate->_($value));
        }
        $this->addElement($type);

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name of the clinic');
        $name->setRequired(true);
        $this->addElement($name);

        $street = new Zend_Form_Element_Text('streetaddress');
        $street->setLabel('Street address');
        $street->setRequired(true);
        $this->addElement($street);

        $city = new Zend_Form_Element_Text('city');
        $city->setLabel('City');
        $city->setRequired(true);
        $this->addElement($city);

        $postcode = new Zend_Form_Element_Text('postcode');
        $postcode->setLabel('Postcode');
        $postcode->setRequired(true);
        $this->addElement($postcode);

        // fetching regions from xml data
        $data = simplexml_load_file(APPLICATION_PATH . '/../data/regions.xml');
        $province = new Zend_Form_Element_Select('province');
        $province->setLabel('Province');
        $province->setRequired(true);
        foreach ($data->country[0]->region as $region) {
            $province->addMultiOption($region['id'], $region);
        }
        $this->addElement($province);

        $this->addDisplayGroup(array('name', 'type', 'streetaddress', 'city', 'postcode', 'province'), 'addressInfo');
        $group = $this->getDisplayGroup('addressInfo');
        $group->setLegend('Address info');

        // reprezentant info

        $representantName = new Zend_Form_Element_Text('repName');
        $representantName->setLabel('Representant name');
        $representantName->setRequired(true);
        $this->addElement($representantName);

        $phone = new Zend_Form_Element_Text('repPhone');
        $phone->setLabel('Representant phone');
        $phone->setRequired(true);
        $this->addElement($phone);


        $this->addDisplayGroup(array('repEmail', 'repName', 'repPhone'), 'representantInfo');
        $group = $this->getDisplayGroup('representantInfo');
        $group->setLegend('Representant info');

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
}