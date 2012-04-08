<?php
class User_Form_Registration extends Twitter_Form
{
    public function init()
    {
		
        $this->setName("register");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'required'   => true,
            'label'      => 'Username',
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Password',
        ));

	    $this->addElement('password', 'password_confirmation', array(
	        'filters'    => array('StringTrim'),
	        'required'   => true,
	        'label'      => 'Repeat password',
	    ));
		
		$submit      = new Zend_Form_Element_Submit('Signin');
        
        $submit->setLabel('Sign In!');
		$this->addElement($submit);
    }

}