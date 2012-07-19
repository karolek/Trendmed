<?php
class Clinic_Form_PasswordRecovery extends Twitter_Form
{
    public function init()
    {
        $this->setName("passwordRecovery");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
                array('EmailAddress'),
            ),
            'required'   => true,
            'label'      => 'Your e-mail',
            'description'=> 'Login to Your account'
        ));
		
		$submit      = new Zend_Form_Element_Submit('Recover');
		$submit->setLabel('Recover');
		        
		$this->addElement($submit);
    }

}