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
        $options = array(
            'decorate' => false,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return $node['name'].count($node['__children']);
            }
        );
        $tree = $repo->findAllAsArrayTree($options);
        // formating the tree for categories
        $output = "";
        foreach($tree as $node) {
            $output .= '<li class="nav-header"><a href="#">'. $node["name"] . '</a><span>(' . count($node['__children']). ')</span></li>';
            if (count($node['__children']) > 0) {
                $output .= '<ul class="hidden">'; //class hidden is for show/hide behavior
                foreach($node['__children'] as $subcategory) {
                    $link   = $this->view->url(array('slug' => $subcategory['slug']), 'category', false);
                    $class = 'class="';
                    if ($subcategory['slug'] == $selected) {
                        $class .= 'active ';
                    }
                    $class .= '" ';
                    $output .= '<li><a href="'.$link.'" ' . $class . '>' . $subcategory['name'] . '</a></li>';
                }
                $output .= '</ul>';
            }
        }
        return $output;
    }

}