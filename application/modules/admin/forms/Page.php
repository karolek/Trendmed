<?php
class Admin_Form_Page extends Twitter_Form
{

    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $config = \Zend_Registry::get('config');

        foreach ($config->languages as $lang) {
            $title = new \Zend_Form_Element_Text('title_' . $lang->code);
            $title->addFilter('StripTags');
            $title->setRequired(true);
            $title->setLabel('Tytuł dla ' . $lang->name);

            $content = new \Zend_Form_Element_Textarea('content_' . $lang->code);
            $content->setLabel('Treść dla ' . $lang->name);

            if($lang->default) {
                $title->setName('title');
                $content->setName('content');
            }
            $this->addElement($title);
            $this->addElement($content);

            unset($title);
            unset($content);

        }
        // lead photo
        $file = new \Zend_Form_Element_File('leadPhoto');
        $file->setLabel('Lead photo');
        $file->setDescription('Zdjęcie przypisane do artykułu');
        // ensure only 1 file
        $file->addValidator('Count', false, 1);
        // limit to 100K
        $file->addValidator('Size', false, 102400 * 10);
        // only JPEG, PNG, and GIFs
        $file->addValidator('Extension', false, 'jpg,png,gif');
        $this->addElement($file);

        // adding page types
        $type = new \Zend_Form_Element_Select('type');
        $type->setLabel('Page type');
        foreach (\Trendmed\Entity\Page::$pageTypes as $key => $label) {
            $type->addMultiOption($key, $label);
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

    public function addPhoto(\Trendmed\Entity\ArticlePhoto $photo, Zend_View_Helper_Abstract $showPhotoHelper)
    {
        $element = $this->getElement('leadPhoto');
        $element->getDecorator('Description')->setEscape(false);
        $element->setDescription($showPhotoHelper->showPhoto($photo, 'small', 'ArticlePhoto').' wybranie nowego zdjęcia napisze aktualne zdjęcie');
    }

}