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

    protected $_photosUsed = 0;
    protected $_photosLeft = 0;

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
        for($i = 0; $i < $config->services->photo->limit; $i++ ) {
            $file = new Zend_Form_Element_File('photo'.$i);
            $file->setLabel('Photo file (jpg, png, gif)');
            // limit to 100K
            $file->addValidator('Size', false, 102400 * 10);
            // only JPEG, PNG, and GIFs
            $file->addValidator('Extension', false, 'jpeg,jpg,png,gif');

            $this->addElement($file);
        }


        $submit = new \Zend_Form_Element_Submit('Zapisz');
        $this->addElement($submit);
    }

    private function _getCategories($parentId = 0, $excludeCategories = null)
    {
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $repo = $em->getRepository('Trendmed\Entity\Category');
        if ($parentId < 1) {
            $tree = $repo->findAllMainCategoriesAsArray();
        } else {
            $tree = $repo->findForParentAsArray($parentId, $excludeCategories);
        }
        // parse the result for
        $map = array();
        foreach ($tree as $node) {
            $map[$node['id']] = $node['name'];
        }
        return $map;
    }

    /**
     * @param \Zend_Form_Element_Select $element Where to add options
     * @param $parentId ID in tree of parent node to fetch all categories from that parent
     * @param sting $selected currently selected option from given $elementName
     * @param \Doctrine\Common\Util\ArrayCollection $excludeCategories
     */
    public function addCategoriesToSelect(\Zend_Form_Element_Select $element,
                                          $parentId,
                                          \Trendmed\Entity\Category $selected = null,
                                          Doctrine\Common\Collections\Collection $excludeCategories = null)
    {
        # excludeCategories is the collection of services to remove from select,
        # we need to remove from that selection a currently edited category
        if($selected AND $excludeCategories AND $parentId > 0 ) {
            # we want to preserve selected category, even if in list of $excludeCategories
            $excludeCategories->removeElement($selected);
        }

        $element->addMultiOptions($this->_getCategories($parentId, $excludeCategories));

        # selecting selected value
        if($selected) {
            $element->setValue($selected->getId());
        }
    }

    ## METHODS FOR SERVICE PHOTOS ##

    /**
     * Set's how many photos clinic uploaded for this service
     * @param $photosUsed integer
     */
    public function setPhotosUsed($photosUsed)
    {
        # removing the file element
        $config = Zend_Registry::get('config');

        for ($i = 0; $i < $photosUsed; $i++) {
            if($this->getElement('photo'.$i)) {
                $this->removeElement('photo'.$i);
            }
        }
        $this->_photosLeft = $config->services->photo->limit - $photosUsed;
        $this->_photosUsed = $photosUsed;
    }

    /**
     * @return int
     */
    public function getPhotosUsed()
    {
        return $this->_photosUsed;
    }

    /**
     * @return int returns how many photos user can still add
     */
    public function getPhotosLeft()
    {
        return $this->_photosLeft;
    }

}