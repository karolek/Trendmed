<?php
class Trendmed_View_Helper_CategoriesTree extends Zend_View_Helper_Abstract
{
    public $view;
    protected $_rootNode;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function categoriesTree($selected = null)
    {
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $root = $repo->getRootNode();
        $output = '';
        if (count($root->getChildren()) > 0 ) {
            $output .= '<ol>';
            foreach($root->children as $topCat) {
                $output .= '<li>';
                $output .= $topCat->name;
                if(count($topCat->children) > 0 ) {
                    $output .= '<ul>';
                    foreach ($topCat->children as $bottomCat) {
                        $output .= '<li>'.$bottomCat->name.'</li>';
                    }
                    $output .= '</ul>';
                }
                $output .= '</li>';
            }
            $output .= '</ol>';
        } else {
            $output = $this->view->translate('No categories in system');
        }
        return $output;

    }

}