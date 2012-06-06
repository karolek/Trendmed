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
        // adding extiting categories to view
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        //ensure that there is allways a root node
        $root = $em->getRepository('\Trendmed\Entity\Category') // this is main root of the menu
            ->findOneByLvl(0);
        if(!$root) {
            throw new \Exception('No root in categories');
        }
        $this->_rootNode = $root; //we need this root to fetch the categories from given root

        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $options = array(
            'decorate' => false,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                //\Zend_Debug::dump($node);
                return $node['name'].count($node['__children']);
            }
        );
        $htmlTree = $repo->childrenHierarchy(
            $this->_rootNode,
            /* starting from root nodes */ false,
            /* load all children, not only direct */ $options
        );

        // formating the tree for categories
        $output = "";
        foreach($htmlTree as $node) {
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