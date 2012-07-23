<?php
class Admin_Form_Page extends Twitter_Form
{

    public function init()
    {
        $this->setName("login");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $config = \Zend_Registry::get('config');


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

        // adding field for sponsored clinic
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->addSponsoredClinicElement($em);


        $i = 100;
        foreach ($config->languages as $lang) {
            $title = new \Zend_Form_Element_Text('title_' . $lang->code);
            $title->addFilter('StripTags');
            $title->setRequired(true);
            $title->setLabel('Tytuł dla ' . $lang->name);
            $content = new \Zend_Form_Element_Textarea('content_' . $lang->code);
            $content->setLabel('Treść dla ' . $lang->name);

            if ($lang->default) {
                $title->setName('title');
                $content->setName('content');
            }
            $this->addElement($title);
            $this->addElement($content);

            unset($title);
            unset($content);

        }

        // submit button, at the end
        $submit = new Zend_Form_Element_Submit('signin');
        $submit->setLabel('Save');

        $this->addElement($submit);

        // hidden for edit
        $this->addElement('hidden', 'id');


    }

    public function addPhoto(\Trendmed\Entity\ArticlePhoto $photo, Zend_View_Helper_Abstract $showPhotoHelper)
    {
        $element = $this->getElement('leadPhoto');
        $element->getDecorator('Description')->setEscape(false);
        $element->setDescription($showPhotoHelper->showPhoto($photo, 'small', 'ArticlePhoto') . ' wybranie nowego zdjęcia napisze aktualne zdjęcie');
    }

    public function addSponsoredClinicElement($em)
    {
        $select = new \Zend_Form_Element_Select('sponsoredByClinic');
        $select->addMultiOption(0, '-- wybierz --');

        // pobieranie wszystkich aktywnych klinik
        $clinics = $em->getRepository('\Trendmed\Entity\Clinic')
            ->findAll();
        if (!$clinics) {
            return false;
        }

        foreach ($clinics as $clinic) {
            $select->addMultiOption($clinic->getId(), $clinic->getName() . ', ' . $clinic->getCity());
        }
        $select->setLabel('Artykuł sponsorowany przez');
        $this->addElement($select);
    }

}