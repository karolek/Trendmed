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
        var_dump($selected);
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $root = $repo->getRootNode();
        $output = '';

        if (count($root->getChildren()) > 0 ) {
            foreach($root->children as $topCat) {
                $output .= '<li class="nav-header"><a href="#">'. $topCat->name . '</a><span>(' . count($topCat->children). ')</span></li>';

                if(count($topCat->children) > 0 ) {
                    $output .= '<ul class="hidden">';
                    foreach ($topCat->children as $bottomCat) {
                        $link   = $this->view->url(array('slug' => $bottomCat->slug), 'category', false);
                        $class = 'class="';
                        if ($bottomCat->slug == $selected and $bottomCat->slug != '') {

                            $class .= 'active';
                        }
                        $class .= '" ';

                        $output .= '<li><a href="'.$link.'" ' . $class . '>' . $bottomCat->name . '</a></li>';
                    }
                    $output .= '</ul>';
                }
                $output .= '</li>';
            }
        } else {
            $output = $this->view->translate('No categories in system');
        }
        return $output;

    }

}