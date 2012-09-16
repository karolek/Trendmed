<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 14.05.12
 * Time: 14:46
 * To change this template use File | Settings | File Templates.
 */
class Admin_Form_DeletePatient extends Twitter_Form
{
    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $id = new \Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Usuń placówke');

        $this->addElement($submit);
    }
}
