<?php
class Patient_ProfileController extends Zend_Controller_Action
{
    public function init()
    {
        // enabling ajax for some actions
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-favorite-clinic', 'html')
            ->initContext();
        $this->_em =  $this->_helper->getEm();

    }

    public function addFavoriteClinicAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $user = $this->_helper->LoggedUser();
            $values = $request->getPost();
            $entityName = $values['entity'];
            $entityId   = $request->getParam('entity_id');
            // Im leaving up to ACL that only patient should be able to add fav clinic
            if (!$user instanceof \Trendmed\Entity\Patient) {
                throw new \InvalidArgumentException('Only logged Patients cad add a fav. clinic');
            }
            $clinic = $this->_em->find($entityName, $entityId);
            if(!$clinic)
                throw new \Exception('No entity with ID ' . $entityId . ' found');
            $user->addFavoriteClinic($clinic);
            $this->_em->persist($user);
        //    $this->_em->persist($clinic);
            exit('a');
            $this->_em->flush();
            $this->view->clinic     = $clinic;
            $this->view->user       = $user;

        } else {
            throw new \Exception('Request should be POST');
        }
    }
}