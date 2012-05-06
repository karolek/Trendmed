<?php

/**
 * Description of Service
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Service extends Twitter_Form
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
        if (!$this->getCategories()) {
            throw new Zend_Form_Exception(
                'Form needs to have categories attached to it'
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

        // price max in EURO (optional)
        $priceMax = new Zend_Form_Element_Text('pricemax');
        $priceMax->setRequired(false);
        $priceMax->setLabel('Cena maksymalna za usługę w EURO (opcjonalnie)');
        $priceMax->addValidator($floatValidator);

    }

    public function setCategories($array)
    {
        $this->_translatedCategoryArrayTree = $array;
    }

    public function getCategories()
    {
        return $this->_translatedCategoryArrayTree;
    }
}