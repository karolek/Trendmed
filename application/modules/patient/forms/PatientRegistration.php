<?php
class Patient_Form_PatientRegistration extends Twitter_Form
{
    public function init()
    {
        $this->setName("register");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        
        $text = new Zend_Form_Element_Text('username');
        $text->addFilters(array('StringTrim', 'StringToLower'));
        $text->setRequired(true);
        $text->setLabel('E-mail Address');
        $validator = new Zend_Validate_Db_NoRecordExists(array(
            'table' => 'user',
            'field' => 'email',
        ));
        $text->addValidator($validator);
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
	    
		$submit      = new Zend_Form_Element_Submit('register');
        
        $submit->setLabel('Reigster for free!');
		$this->addElement($submit);
    }

}