<?php
class Admin_Form_Page extends Twitter_Form
{

    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $this->addElement('text', 'title', array(
            'filters'    => array('StripTags'),
            'required'   => true,
            'label'      => 'Page title',
        ));

        $this->addElement('textarea', 'content', array(
            'label'      => 'Page content',
        ));

        // adding page types
        $config = \Zend_Registry::get('config');
        $type = new \Zend_Form_Element_Select('type');
        $type->setLabel('Page type');
        foreach ($config->pages->types as $typ) {
            $type->addMultiOption($typ, $typ);
        }
        //$type->setDescription('Type will determine some minor features. E.g. Articles and Camera Reviews can be liked
        //by users, text pages don\'t. Use Text pages for general information, like policies, about us, terms and conditions etc.');
        $this->addElement($type);

        $submit      = new Zend_Form_Element_Submit('signin');
        $submit->setLabel('Save');

        $this->addElement($submit);

        // hidden for edit
        $this->addElement('hidden', 'id');
    }

}