<?php

/**
 * Description of Service
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_Form_Service extends Twitter_Form
{
    /**
     * @var array Translated Category Array Tree
     */
    protected $_translatedCategoryArrayTree = null;

    public function init()
    {
        $config = Zend_Registry::get('config');
        $this->setMethod('post');

        // category
        if (!$categories = $this->_getCategories()) {
            throw new Zend_Form_Exception(
                'No categories defined in DB'
            );
        }
        // TODO: make selects with parent and children categories

        // description of the service should be translatable with html editor
        foreach ($config->languages as $lang) {
            $description = new Zend_Form_Element_Textarea('description_'.$lang->code);
            $description->setRequired(true);
            $description->setAttrib('class', 'ckeditor');
            $description->setLabel(
                'Opis wykonywanej usługi w języku: ' . $lang->name
            );
            $this->addElement($description);
        }
        // price min in EURO
        $priceMin = new Zend_Form_Element_Text('pricemin');
        $priceMin->setRequired(true);
        $priceMin->setLabel('Cena minimalna za usługę w EURO');
        $floatValidator = new Zend_Validate_Float();
        $priceMin->addValidator($floatValidator);
        $this->addElement($priceMin);

        // price max in EURO (optional)
        $priceMax = new Zend_Form_Element_Text('pricemax');
        $priceMax->setRequired(false);
        $priceMax->setLabel('Cena maksymalna za usługę w EURO (opcjonalnie)');
        $priceMax->addValidator($floatValidator);
        $this->addElement($priceMax);

        $submit = new \Zend_Form_Element_Submit('Save');
        $this->addElement($submit);
    }

    private function _getCategories()
    {
        if(!$this->_translatedCategoryArrayTree) {
            $em = \Zend_Registry::get('doctrine')->getEntityManager();
            $repo = $em->getRepository('Trendmed\Entity\Category');
            $tree = $repo->childrenHierarchy();
            $this->_translatedCategoryArrayTree = $tree[0];
        }
        return $this->_translatedCategoryArrayTree;
    }
}