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

        $this->addElement('text', 'name', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(1, 50)),
            ),
            'required' => true,
            'label' => 'Name',
        ));

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