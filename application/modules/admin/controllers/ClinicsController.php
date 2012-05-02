<?php
use Doctrine\ORM\Tools\Pagination\Paginator;

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
        $request = $this->getRequest();
        
        $qb = $this->_helper->getEm()->createQueryBuilder();
        $qb->select('c')
                ->from('\Trendmed\Entity\Clinic', 'c');
        $qb->setMaxResults(50);
        $qb->setFirstResult(0);
        
        $query = $qb->getQuery();
        
        
        $paginator = new Paginator($query, $fetchJoin = true);
        $this->view->paginator = $paginator;
    }
}
?>
