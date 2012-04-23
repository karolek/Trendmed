<?php
class Admin_CategoriesController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $categoryTable = new Catalog_Model_DbTable_Category();
        $categories = $categoryTable->fetchAll();
        $this->view->categories = $categories;
    }
    
    public function saveAction()
    {
        $request = $this->getRequest();
        $form = new Admin_Form_Category();
        $rowId = $request->getParam('category_id', null);
        $categoryTable = new Catalog_Model_DbTable_Category();

        if($modelId) {
            $row = $categoryTable->find($rowId);
        } else {
            $row = $categoryTable->createRow();
        }
        
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $row->setFromArray($values);
                $row->save();
                $this->_helper->FlashMessenger(array('success' => 
                    'Kategoria zapisana'));
                $this->_helper->Redirector('index');
             } else {
                 $this->_helper->FlashMessenger(array('warning' => 'Wypełnij poprawnie
                     formularz'));
             }
        }
        $form->populate($row->toAray());
        $this->view->form = $form;
    }
    
    public function deleteAction()
    {
        
    }
}