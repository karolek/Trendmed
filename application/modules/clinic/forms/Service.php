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

        $categorySelect = new Zend_Form_Element_Select('categories');
        $categorySelect->setLabel('Service category');
        $categorySelect->addMultiOption('0', '-- WYBIERZ --');
        $categorySelect->addMultiOptions($categories);
        $this->addElement($categorySelect);
        //$this->addElement('select', 'fruits', array('label'=>'Fruits','required'=> true,'multioptions'=> $categories));

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
        $priceMax->setRequired(false);
        $priceMax->setLabel('Cena maksymalna za usługę');
        $priceMax->setDescription('W walucie EURO');
        $priceMax->addValidator($floatValidator);
        $priceMax->addValidator(new Me_Validate_ServicePrice());
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
            if (count($tree[0]['__children'])) { //checking if first found root has some categories
                foreach($tree[0]['__children'] as $mainCategory) { //iterating though main catgegories
                    if (count($mainCategory['__children']) > 0 ) { // checking if main cat. has children cats
                        $map[$mainCategory['name']] = array(); // instancing the main category in array
                        foreach ($mainCategory['__children'] as $subCategory) {
                            $map[$mainCategory['name']][$subCategory['id']] = $subCategory['name'];
                        }
                    }
                }
            }
            $this->_translatedCategoryArrayTree = $map;
        }
        return $this->_translatedCategoryArrayTree;
    }
}