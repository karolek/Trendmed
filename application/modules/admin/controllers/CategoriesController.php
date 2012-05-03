<?php
use \Trendmed\Entity\Category;
class Admin_CategoriesController extends Zend_Controller_Action
{
    protected $_rootNode; 
    
    public function init()
    {
        $em = $this->_helper->getEm();

        //ensure that there is allways a root node
        $root = $em->getRepository('\Trendmed\Entity\Category') // this is main root of the menu
                            ->findOneByRoot(1);
        if(!$root) {
            $root = new \Trendmed\Entity\Category;
            $root->name = 'root';
            $em->persist($root);
            $em->flush();
        }
        $this->_rootNode = $root;
    }
    
    public function indexAction() {
        $em = $this->_helper->getEm();
        $repo = $em->getRepository('\Trendmed\Entity\Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a class="confirm" href="/admin/categories/delete/node_id/'.$node['id'].'">' . $node['name'] . '</a>';
            }
        );
        $htmlTree = $repo->childrenHierarchy(
                $this->_rootNode, /* starting from root nodes */ false, /* load all children, not only direct */ $options
        );
        $this->view->categories = $htmlTree;
        $form = new \Admin_Form_Category(); 
        $form->setAction($this->_helper->url('save', 'categories'));
        $this->view->form = $form;
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        $form = new Admin_Form_Category();
        $modelId = $request->getParam('category_id', null);
        $em = $this->_helper->getEm();

        if ($modelId) {
            $model = $em->find('\Trendmed\Entity\Category', $modelId);
        } else {
            $model = new \Trendmed\Entity\Category();
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $model->setOptions($values);
                // fetching parent
                if ($values['parent_id'] == 0) {
                    // setting the root parent
                    $parent = $this->_rootNode;
                } else {
                    $parent = $em->getRepository('\Trendmed\Entity\Category')
                            ->find($values['parent_id']);
                }
                if(!$parent) {
                    throw new Exception('Category must have a parent');
                }
                $model->setParent($parent);
                $em->persist($model);
                $em->flush();
                $this->_helper->FlashMessenger(array('success' =>
                    'Category saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Correct the errors in the form'));
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
        $this->_helper->FlashMessenger(array('success' => 'Category has been deleted.
            All children elements has been reordered.'));
        $this->_helper->Redirector('index');
    }
}