<?php
class Patient_Form_Connect extends Twitter_Form
{
    public function init()
    {
        $this->setName("connect");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 50)),
            ),
            'required'   => true,
            'label'      => 'Password',
        ));


        $submit      = new Zend_Form_Element_Submit('signin');
        $submit->setLabel('Connect accounts');

        $this->addElement($submit);
    }


}