<?php
class Clinic_Form_Login extends Twitter_Form
{

    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'E-mail kontaktowy:',
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Hasło:',
        ));
		
		$submit      = new Zend_Form_Element_Submit('signin');
		$submit->setLabel('Sign In!');
		
		$rememberMe = new Zend_Form_Element_Checkbox('rememberMe');
		$rememberMe->setLabel('Remember me for 7 days');
		$this->addElement($rememberMe);
        
		$this->addElement($submit);
    }

}