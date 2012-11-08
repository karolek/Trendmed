<?php
class Patient_Form_ChangeEmail extends Twitter_Form
{
    public function init()
    {
        $this->setName("changeEmail");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $passwordValidator = new Zend_Validate_StringLength(array('min' => 6, 'max' => 20, 'encoding' => 'utf-8'));
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Account password',
            'validators' => array($passwordValidator),
            'description'=> 'For security reasons please type in your account password'
        ));

        $text = new Zend_Form_Element_Text('emailaddress');
        $text->addFilters(array('StringTrim', 'StringToLower'));
        $text->setRequired(true);
        $text->setLabel('E-mail Address');
        $validator = new Zend_Validate_Db_NoRecordExists(array(
            'table' => 'patients',
            'field' => 'login',
        ));
        $text->addValidator($validator);
        $validator = new Zend_Validate_EmailAddress();
        $text->addValidator($validator);
        $this->addElement($text);

        $submit      = new Zend_Form_Element_Submit('submit');


        $submit->setLabel('Change e-mail');
        $this->addElement($submit);
    }

}