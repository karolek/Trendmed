<?php
/**
 * Controller takes care of displaying the clinics in categories
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
class Catalog_CategoriesController extends \Zend_Controller_Action
{

    protected $_em; //entity manager
    protected $_repo; // categories repository, for use of use

    public function init()
    {
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Category');
    }

    public function viewAction()
    {
        $category = $this->_fetchCategoryFromParams();
        $this->view->category = $category;
        $this->view->headTitle($category->name);
        $this->_helper->_layout->setLayout('homepage');
        $this->view->selectedCategory = $category->slug;
    }

    protected function _fetchCategoryFromParams()
    {
        $request = $this->getRequest();
        $slug = $request->getParam('slug');
        if(!$slug) throw new \Exception('No param given in '.__FUNCTION__);
        // fetching the category
        $category = $this->_repo->findOneBySlug($slug);
        if(!$category) throw new \Exception('No category with slug: '.$slug, 404);
        return $category;
    }

}

