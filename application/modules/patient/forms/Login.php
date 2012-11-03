<?php
class Patient_Form_Login extends Me_User_Form_Login
{

    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'required'   => true,
            'label'      => 'Login:',
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Password',
        ));
		
		$submit      = new Zend_Form_Element_Submit('signin');
		$submit->setLabel('Sign In!');
		
		$rememberMe = new Zend_Form_Element_Checkbox('rememberMe');
		$rememberMe->setLabel('Remember me for 7 days');

		$this->addElement($rememberMe);
        
		$this->addElement($submit);
    }
}