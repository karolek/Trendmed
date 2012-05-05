<?php
/**
 * Form for editing and adding new categories to services menu 
 */
class Admin_Form_Category extends Twitter_Form
{

    public function init() {
        $this->setName('login');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $config = \Zend_Registry::get('config');
        
        foreach ($config->languages as $lang) {
            $nameElement = new \Zend_Form_Element_Text('name_'.$lang->code);
            $nameElement->setRequired(true);
            $nameElement->addValidators(array(
                array('StringLength', false, array(1, 50)),
            ));
            $nameElement->setLabel($lang->name.' nazwa');
            $this->addElement($nameElement);
            unset($nameElement);
            
            $descriptionElement = new \Zend_Form_Element_Textarea(
                'description_'.$lang->code);
            $descriptionElement->setRequired(true);
            $descriptionElement->addValidators(array(
                array('StringLength', false, array(1, 250)),
            ));
            $descriptionElement->setLabel($lang->name.' opis');
            $this->addElement($descriptionElement);
        }
        $select = new \Zend_Form_Element_Select('parent_id');
        $select->setLabel('Position');
        $select->addMultiOption(0, '-- top --');
        $tree = self::getParentCategories();
        foreach($tree as $node) {
            $select->addMultiOption($node['id'], $node['name']);
        }
        $this->addElement($select);
        
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setLabel('Save');
        $this->addElement($submit);
    }
    
    private static function getParentCategories()
    {
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $repo = $em->getRepository('Trendmed\Entity\Category');
        $query = $em
                ->createQueryBuilder()
                ->select('node')
                ->from('Trendmed\Entity\Category', 'node')
                ->orderBy('node.root, node.lft', 'ASC')
                ->where('node.root = 1')
                ->andWhere('node.lvl = 1')
                ->getQuery();
        
        //$options = array('decorate' => true);
        $tree = $query->getArrayResult();
        return $tree;
    }

}