<?php
class Patient_Form_PatientRegistration extends Twitter_Form
{

    protected $_em;

    public function init()
    {
        if(!$this->_em) {
            $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        }
        $this->setName("register");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        
        $text = new Zend_Form_Element_Text('login');
        $text->addFilters(array('StringTrim', 'StringToLower'));
        $text->setRequired(true);
        $text->setLabel('E-mail address');

        $validator = new Zend_Validate_EmailAddress();
        $text->addValidator($validator);
        $this->addElement($text);

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


        $terms = new Zend_Form_Element_Checkbox('terms');

        $terms->setLabel('I agree to accept the site <a href="/page/regulamin-dla-pacjentow" target="_blank">Rules and Terms & Conditions</a>');
        $terms->setRequired(true);
        $checkboxValidator = new Zend_Validate_InArray(array(1));
        $checkboxValidator->setMessage('Agreement is required', Zend_Validate_InArray::NOT_IN_ARRAY);
        $terms->addValidator($checkboxValidator); //  litle trick to get the validation
        $this->addElement($terms);


        $newsletter = new Zend_Form_Element_Checkbox('isNewsletterActive');
        $newsletter->setLabel('Please include me in Trendmed.eu updates (optional)');
        $this->addElement($newsletter);
		$submit      = new Zend_Form_Element_Submit('register');
        
        $submit->setLabel('Register for free!');
		$this->addElement($submit);
    }


    /**
     * Overrides superclass method to add just-in-time validation for NoEntityExists-type validators that
     * rely on knowing the id of the entity in question.
     * @param type $data
     * @return type
     */
    public function isValid($data) {
        $unameUnique = new \TimDev\Validate\Doctrine\NoEntityExists(
            array('entityManager' => $this->_em,
                'class' => 'Trendmed\Entity\Patient',
                'property' => 'login',
                'exclude' => array(
                    //array('property' => 'id', 'value' => $this->getValue('id'))
                )
            )
        );
        $unameUnique->setMessage(
            'Another user already has username "%value%"',  \TimDev\Validate\Doctrine\NoEntityExists::ERROR_ENTITY_EXISTS
        );

        $this->getElement('login')->addValidator($unameUnique);

        return parent::isValid($data);
    }

}