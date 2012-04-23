<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClinicsController
 *
 * @author Bard
 */
class Admin_ClinicsController extends Zend_Controller_Action {
    
    
    /**
     * Displays list of clinics 
     */
    public function indexAction() {
        $this->view->headTitle('Lista zarejestrowanych obiektów');
        $clinicMapper = new Clinic_Model_ClinicMapper();
        $request = $this->getRequest();
        $adapter = new Zend_Paginator_Adapter_DbTableSelect($clinicMapper
                ->getDbTable()
                ->select()
                );

        $paginator = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage(1);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
        $this->view->paginator = $paginator;
    }
}
?>
