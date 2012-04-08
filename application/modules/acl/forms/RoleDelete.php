<?php

class Acl_Form_RoleDelete extends Twitter_Form {
	
	
	public function init()
	{
		$this->setName('roleDelete');
		$this->setMethod('post');
		$this->setAttrib('class', 'form-horizontal');
		$this->setAction('delete-role');
		$this->addElement('hidden', 'id', array(
				'filters'	=>	array('Int')
		));
		

		$submit      = new Zend_Form_Element_Button('submit');
		
		$submit->setLabel('Delete');
		$this->addElement($submit);
		
	}
}