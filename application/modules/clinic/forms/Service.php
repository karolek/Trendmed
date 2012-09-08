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
        if (!$mainCategories = $this->_getCategories()) {
            throw new \Exception(
                'No categories defined in DB'
            );
        }

        // adding main category selector
        $categorySelect = new Zend_Form_Element_Select('mainCategory', array(
            'escape' => false,
        ));
        $categorySelect->setLabel('Service main category');
        $categorySelect->addMultiOption('0', '-- WYBIERZ --');
        $categorySelect->addMultiOptions($mainCategories);
        $categorySelect->addValidator('GreaterThan', false, array('min' => 1));

        $this->addElement($categorySelect);

        // this is subcategory selecter, should be populated with ajax req. based on main category selection
        $subcategorySelect = new \Zend_Form_Element_Select('subCategory');
        $subcategorySelect->setLabel('Service sub-category');
        $subcategorySelect->addMultiOption(0, '-- WYBIERZ GŁÓWNĄ KATEGORIE --');
        $subcategorySelect->addValidator('GreaterThan', false, array('min' => 1));


        $this->addElement($subcategorySelect);

        // description of the service should be translatable with html editor
        foreach ($config->languages as $lang) {

            $description = new Zend_Form_Element_Textarea('description_'.$lang->code);
            $description->setRequired(true);
            $description->setAttrib('class', 'ckeditor');
            $description->setLabel(
                'Opis wykonywanej usługi w języku: ' . $lang->name
            );

            // overwriting for default values
            if($lang->default) {
                $description->setName('description');
            }
            $this->addElement($description);
        }

        // price min in EURO
        $priceMin = new Zend_Form_Element_Text('pricemin');
        $priceMin->setRequired(true);
        $priceMin->setLabel('Cena minimalna za usługę');
        $priceMin->setDescription('W walucie EURO');
        $floatValidator = new Zend_Validate_Float();
        $priceMin->addValidator($floatValidator);
        $valid  = new Zend_Validate_GreaterThan(array('min' => 1));
        $priceMin->addValidator($valid);
        $this->addElement($priceMin);


        // price max in EURO (optional)
        $priceMax = new Zend_Form_Element_Text('pricemax');
        $priceMax->setRequired(true);
        $priceMax->setLabel('Cena maksymalna za usługę');
        $priceMax->setDescription('W walucie EURO');
        $priceMax->addFilter('StringTrim');
        $priceMax->addFilter('StripTags');
        $priceMax->addValidator($floatValidator);
        $priceMax->addValidator(new Me_Validate_ServicePrice());
        $this->addElement($priceMax);

        # photo objects
        # for($i = 1; $i <= $config->services->photo->limit; $i++ ) {
            $file = new Zend_Form_Element_File('photo');
            $file->setLabel('Photo file (jpg, png, gif)');
            // limit to 100K
            $file->addValidator('Size', false, 102400 * 10);
            // only JPEG, PNG, and GIFs
            $file->addValidator('Extension', false, 'jpg,png,gif');

            $this->addElement($file);
        #}


        $submit = new \Zend_Form_Element_Submit('Zapisz');
        $this->addElement($submit);
    }

    private function _getCategories($parentId = 0)
    {
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $repo = $em->getRepository('Trendmed\Entity\Category');
        if ($parentId < 1) {
            $tree = $repo->findAllMainCategoriesAsArray();
        } else {
            $tree = $repo->findForParentAsArray($parentId);
        }
        // parse the result for
        $map = array();
        foreach ($tree as $node) {
            $map[$node['id']] = $node['name'];
        }
        return $map;
    }

    public function addCategoriesToSelect($elementName, $parentId, $selected = null)
    {
        $select = $this->getElement($elementName);
        $select->addMultiOptions($this->_getCategories($parentId));
        if($selected) {
            $select->setValue($selected);
        }
    }

}