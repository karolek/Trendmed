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


    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
    }

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

    public function changeActiveStateAction()
    {
        $request = $this->getRequest();
        $newState = $request->getParam('new-state');

        $clinic = $this->_fetchClinicFromParams();

        switch($newState) {
            case 1:
                $clinic->activate();
                $this->_helper->FlashMessenger(array(
                    'info' => 'Klinika aktywowana'
                    )
                );
                break;
            case 0:
                $clinic->deactivate();
                $this->_helper->FlashMessenger(array(
                    'info' => 'Klinika deaktywowana'
                    )
                );
                break;
            default:
                throw new \Exception('Undefined state given in '.__FUNCTION__);
                break;
        }
        $this->_em->persist($clinic);
        $this->_em->flush();

        $this->_helper->Redirector('index');
    }

    public function deleteClinicAction()
    {
        $request = $this->getRequest();

        $this->view->HeadTitle('Usuwanie placówki z portalu');

        $clinic = $this->_fetchClinicFromParams();

        $form       = new Admin_Form_DeletePatient();

        if($request->isPost()) {
            $this->_em->remove($clinic);
            $this->_em->flush();

            $this->_helper->FlashMessenger(array(
                'warning' => 'Placówka o loginie: '. $clinic->getLogin(). ' została usunięta'
            ));
            $this->_helper->Redirector('index');
        }
        $this->view->user = $clinic;
        $this->view->form = $form;


    }

    public function regeneratePasswordForClinic()
    {

    }

    protected function _fetchClinicFromParams()
    {
        $request = $this->getRequest();
        $repo = $this->_em->getRepository('\Trendmed\Entity\Clinic');

        if($request->getParam('id')) {
            $userId    = $request->getParam('id');
            $user      = $this->_em->find('\Trendmed\Entity\Clinic', $userId);
            if(!$user) throw new \Exception('No clinic found');
        } else {
            throw new \Exception('bad parameters given in '.__FUNCTION__);
        }
        return $user;
    }
}
?>
