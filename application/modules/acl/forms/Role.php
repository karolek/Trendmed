<?php
class Acl_Form_Role extends Twitter_Form
{

    public function init()
    {
        $this->setName("role");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 25)),
				array('alnum'),
            ),
            'required'   => true,
            'label'      => 'Role name',
        ));

		$this->addElement('hidden', 'id', array(
			'filters'	=>	array('Int')
			));

		
		$submit = new Zend_Form_Element_Submit('Submit');
        
        $submit->setLabel('Save');
		$this->addElement($submit);

    }

}