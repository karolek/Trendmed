<?php
class User_Form_Registration extends Twitter_Form
{
    public function init()
    {
		
        $this->setName("register");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        
        $text = new Zend_Form_Element_Text('username');
        $text->addFilters(array('StringTrim', 'StringToLower'));
        $text->setRequired(true);
        $text->setLabel('Username');
        $validator = new Zend_Validate_Db_NoRecordExists(array(
            'table' => 'acluser',
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
	        'validators' => array($passwordValidator),
	    ));
	    
		
		$submit      = new Zend_Form_Element_Submit('Signin');
        
        $submit->setLabel('Sign In!');
		$this->addElement($submit);
    }

}