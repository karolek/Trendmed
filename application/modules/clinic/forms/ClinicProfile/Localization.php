<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 07.05.12
 * Time: 22:40
 * To change this template use File | Settings | File Templates.
 */
class  Clinic_Form_ClinicProfile_Localization extends Twitter_Form
{
    public function init()
    {
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
    }
}
