<?php
class Clinic_Form_ClinicRegistration extends Twitter_Form
{

    public function init()
    {
        $this->setName("register");
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
        
        $this->addDisplayGroup(
            array('name', 'type', 'streetaddress', 'city', 'postcode', 'province'),
            'addressInfo'
        );
        $group = $this->getDisplayGroup('addressInfo');
        $group->setLegend('Address info');
        
        // reprezentant info        
        
        $email = new Zend_Form_Element_Text('repEmail');
        $email->setLabel('Email address');
        $email->addValidator('EmailAddress');
        $email->setRequired(true);
        $email->setDescription('This will be Your account login');

        $this->addElement($email);
        // rep email must be unique

        $uniqueRepEmailValidator = new Zend_Validate_Db_NoRecordExists(
            array(
                'table' => 'clinics',
                'field' => 'repEmail'
            )
        );
		$email->addValidator($uniqueRepEmailValidator);
		
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
        
        // account info
        
        $passwordValidator = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20, 'encoding' => 'utf-8'));
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Password',
            'validators' => array($passwordValidator),
        ));
        
	    $this->addElement('password', 'password_confirmation', array(
	        'filters'    => array('StringTrim'),
	        'required'   => true,
	        'label'      => 'Repeat password',
	        'validators' => array($passwordValidator, array('identical', false, array('token' => 'password'))),
	    ));
        
        $this->addDisplayGroup(array('password', 'password_confirmation'), 'accountInfo');
        $group = $this->getDisplayGroup('accountInfo');
        $group->setLegend('Account info');

        $terms = new Zend_Form_Element_Checkbox('regulamin');
        $terms->setLabel('Rejestrując się zgadzam się na <a href="/page/regulamin-dla-klinik">warunki regulaminu</a> serwisu.');
        $terms->setRequired(true);
        $checkboxValidator = new Zend_Validate_InArray(array(1));
        $checkboxValidator->setMessage('Akceptacja regulaminu jest wymagana', Zend_Validate_InArray::NOT_IN_ARRAY);
        $terms->addValidator($checkboxValidator); //  litle trick to get the validation
        $this->addElement($terms);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Sign in!');
        $this->addElement($submit);
        
    }


}