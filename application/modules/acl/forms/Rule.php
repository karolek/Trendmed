<?php
class Acl_Form_Rule extends Twitter_Form
{
    protected $_acl;
    protected $_view;
    
    public function setAcl(Br_Acl_Acl $acl) 
    {
        $this->_acl = $acl;
    }

    public function getAcl() 
    {
        return $this->_acl;
    }
    
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
        return $this;
    }
    
    public function getView() 
    {
        return $this->_view;
    }
    public function init()
    {
        $acl = $this->getAcl();
        if(!$acl) {
            throw new Exception("Rule form requires a ACL object, add it by calling setAcl method", 500);
            
        }
        $this->setName("rule");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        
        // role 
        $role = new Zend_Form_Element_Select('role_id');
        $role->setLabel('Rola');
        
        // fetching all roles
        $rolesTable = new Acl_Model_DbTable_Aclrole();
        foreach($rolesTable->fetchAll() as $roleRow) {
            $role->addMultiOption($roleRow->id, $roleRow->name);
        }
        $this->addElement($role);
        
        // module
        $module = new Zend_Form_Element_Select('module');
        $module->setLabel('Moduł');
        
        $module->addMultiOption('%', '%');
        $log = Zend_Registry::get('logger');
        $log->debug($this->_acl); 
        // fetching all modules from the ACL
        foreach($this->_acl->getModulesNames() as $key => $value) {
            $module->addMultiOption($value, $value);
        }
        
        $this->addElement($module);
        
        // controller
        $controller = new Zend_Form_Element_Select('controller');
        $controller->setLabel('Kontroler');
        $controller->addMultiOption('%', '%');
        
        $this->addElement($controller);
        
        // action
        $action = new Zend_Form_Element_Select('action');
        $action->setLabel('Akcja');
        $action->addMultiOption('%', '%');
        
        $this->addElement($action);
        
		$submit = new Zend_Form_Element_Submit('Submit');        
        $submit->setLabel('Save');
		$this->addElement($submit);
		
		$this->_addAjaxBehaviour();
    }
    
    protected function _addAjaxBehaviour()
    {
        $view = $this->getView();
        if(!$view) throw new Exception("Adding Ajax behaviur to rule form requires to add view object in param init array", 500);
        
        $jquery = $view->jQuery();
        
        $statement = 'console.log($("#module"));';
        
        $jquery->addOnLoad($statement);
    }
}