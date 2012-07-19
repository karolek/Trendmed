<?php
class Patient_Form_NewPassword extends Twitter_Form
{

    public function setNotFromToken()
    {

        // adding old password for verify
        $passwordValidator = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20, 'encoding' => 'utf-8'));
        $this->addElement('password', 'old_password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Old password',
            'validators' => array($passwordValidator),
            'order'      => 0,
        ));

        $this->removeElement('token');
    }


    public function init()
    {
        $this->setName("newpassword");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $passwordValidator = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20, 'encoding' => 'utf-8'));
        $this->addElement('password', 'old_password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Old password',
            'validators' => array($passwordValidator),
        ));
        //adding token hidden field
        $this->addElement('hidden', 'token');


        $passwordValidator = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20, 'encoding' => 'utf-8'));
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'New password',
            'validators' => array($passwordValidator),
        ));
        
	    $this->addElement('password', 'password_confirmation', array(
	        'filters'    => array('StringTrim'),
	        'required'   => true,
	        'label'      => 'Repeat new password',
	        'validators' => array($passwordValidator, array('identical', false, array('token' => 'password'))),
	    ));
	    
		$submit      = new Zend_Form_Element_Submit('register');

        $submit->setLabel('Set new password');
		$this->addElement($submit);
    }

}