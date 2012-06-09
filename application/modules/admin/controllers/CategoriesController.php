<?php
use \Trendmed\Entity\Category;
class Admin_CategoriesController extends Zend_Controller_Action
{
    protected $_rootNode;

    protected function _getTree()
    {
        $em = $this->_helper->getEm();

        //ensure that there is allways a root node
        $root = $em->getRepository('\Trendmed\Entity\Category') // this is main root of the menu
            ->findOneByLvl(0);
        if(!$root) {
            $root = new \Trendmed\Entity\Category;
            $root->setName('root');
            $em->persist($root);
            $em->flush();
        }
        $this->_rootNode = $root;

        // adding extiting categories to view
        $em = $this->_helper->getEm();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return $node['name'] . ' <a href="/admin/categories/save/category_id/'.$node['id'].'"><i class="icon-pencil"></i></a>
                <a class="confirm" href="/admin/categories/delete/node_id/'.$node['id'].'"><i class="icon-trash"></i></a>
                <a class="ajax" href="/admin/categories/move-category/node_id/'.$node['id'].'/direction/up"><i class="icon-arrow-up"></i></a>
                <a class="ajax" href="/admin/categories/move-category/node_id/'.$node['id'].'/direction/down"><i class="icon-arrow-down"></i></a>
                ';

            }
        );
        $tree = $repo->findAllAsArrayTree($options);
        return $tree;
    }
    
    public function init()
    {
        $this->view->categories = $this->_getTree();
        // adding javascript for tree managemtn
        $this->view->headScript()->appendFile('/js/admin/categoriesTree.js');
    }
    
    public function indexAction() {

        $form = new \Admin_Form_Category();
        $form->setAction($this->_helper->url('save', 'categories'));
        $this->view->form = $form;
        $this->view->headTitle('Zarządzanie kategoriami');
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        $form = new Admin_Form_Category();
        $modelId = $request->getParam('category_id', null);
        $em = $this->_helper->getEm();
        $config = \Zend_Registry::get('config');
        $repository = $em->getRepository('Gedmo\Translatable\Entity\Translation');

        if ($modelId) {
            $model = $em->find('\Trendmed\Entity\Category', $modelId);
            $translations = $repository->findTranslations($model);
            $form->setDefault('name', $model->getName());
            $form->setDefault('description', $model->getDescription());
            foreach($translations as $transCode => $trans) {
                $form->setDefault('name_'.$transCode, $trans['name']);
                $form->setDefault('description_'.$transCode, $trans['description']);
            }
            $this->view->headTitle('Edycja kategorii '.$model->getName());
        } else {
            $model = new \Trendmed\Entity\Category();
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();

                foreach ($config->languages as $lang) {
                    
                    if ($lang->default == true) { // we must add default values to our main entity
                        $model->setName($values['name']);
                        $model->setDescription(
                            $values['description']
                        );
                        continue;
                    }
                    $repository->translate(
                        $model, 'name', $lang->code, 
                        $values['name_'.$lang->code]
                    );
                    $repository->translate(
                        $model, 'description', $lang->code, 
                        $values['description_'.$lang->code]
                    );
                }
                // fetching parent
                if ($values['parent_id'] == 0) {
                    // setting the root parent
                    $parent = $this->_rootNode;
                } else {
                    $parent = $em->getRepository('\Trendmed\Entity\Category')
                            ->find($values['parent_id']);
                }
                if (!$parent) {
                    throw new Exception('Category must have a parent');
                }
                $model->setParent($parent);
                $em->persist($model);
                $em->flush();

                $this->_helper->FlashMessenger(
                    array('success' => 'Category saved')
                );
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(
                    array('warning' => 'Correct the errors in the form')
                );
            }
        }
        $this->view->form = $form;
        $this->_helper->viewRenderer('index');
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        $nodeId = $request->getParam('node_id');
        $em = $this->_helper->getEm();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $node = $repo->find($nodeId);
        if(!$node) {
            throw new \Exception('No node found with ID:'. $nodeId.', cant
                delete node');
        }
        $repo->removeFromTree($node);
        $em->clear();
        $this->_helper->FlashMessenger(array('success' => 'Category has been deleted. All children elements has been reordered.'));
        $this->_helper->Redirector('index');
    }

    public function moveCategoryAction()
    {
        $request = $this->getRequest();
        $nodeId = $request->getParam('node_id');
        $em = $this->_helper->getEm();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $node = $repo->find($nodeId);
        if(!$node) {
            throw new \Exception('No node found with ID:'. $nodeId.', cant
                delete node');
        }
        $direction  = $request->getParam('direction');
        $toEnd      = (bool) $request->getParam('toEnd', false);
        if($toEnd !== FALSE) {
            $toEnd = 1; // move by one place
        }
        // costruncting method name
        $method = 'move' . ucfirst($direction);
        $repo->$method($node, 1);

        // verification and recovery of tree
        $repo->verify();
// can return TRUE if tree is valid, or array of errors found on tree
        $result = $repo->recover();
        $em->clear(); // clear cached nodes
        if($request->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo $this->_getTree();
        } else {

            $this->_helper->Redirector('index');
        }
    }
}