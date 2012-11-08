<?php
class Patient_Form_Details extends Twitter_Form
{
    protected $_config;

    public function init()
    {
        $this->setName("patient-details");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        // country select
        // we need to get countries from config
        if(!$this->_config) {
            $this->_config = \Zend_Registry::get('config');
        }
        if(empty($this->_config->usersAccounts->countries)) {
            throw new \Exception('No countries defined in application ini for patients');
        }

        $country = new \Zend_Form_Element_Select('country');
        // first we need to add option for none
        $country->addMultiOption(null, '-- please choose --');
        foreach($this->_config->usersAccounts->countries as $code => $name) {
            $country->addMultiOption($code, $name);
        }
        $country->setLabel('Your country');
        $country->setDescription('Please, tell us from what country you are');
        $this->addElement($country);

        // title
        if(empty($this->_config->usersAccounts->titles)) {
            throw new \Exception('No titles defined in application ini for patients');
        }
        $title = new \Zend_Form_Element_Select('title');
        $title->addMultiOption(null, '-- please choose --');
        foreach($this->_config->usersAccounts->titles as $titleName)
        {
            $title->addMultiOption($titleName, $titleName);
        }
        $title->setLabel('Title');
        $this->addElement($title);

        // user real name
        $name = new \Zend_Form_Element_Text('name');
        $name->addFilter('StripTags');
        $name->setLabel('Your real name');
        $this->addElement($name);

        // user phone number
        $phoneNumber = new \Zend_Form_Element_Text('phoneNumber');
        $phoneNumber->setLabel('Your phone number');
        # $phoneNumber->setDescription('Start with your country prefix, e.g. +48 for Poland');
        $this->addElement($phoneNumber);

		$submit      = new Zend_Form_Element_Submit('register');
		$submit->setLabel('Save');

		$this->addElement($submit);
    }

    public function populateFromObject(\Trendmed\Entity\Patient $patient)
    {
        $this->populate(array(
            'country'       => $patient->getCountry(),
            'name'          => $patient->getName(),
            'phoneNumber'   => $patient->getPhoneNumber(),
            'title'         => $patient->getTitle(),
        ));

    }

}