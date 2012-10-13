<?php
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Maintnance of patients in the system
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Admin_ReservationsController extends Zend_Controller_Action {


    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
    }

    public function patientReservationsAction()
    {
        $this->view->headTitle('Rezerwacje pacjenta');
        $this->view->patient = $this->_fetchPatientFromParams();
    }

    public function clinicReservationsAction()
    {
        $this->view->headTitle('Rezerwacje kliniki');
        $this->view->clinic = $this->_fetchClinicFromParams();
    }

    protected function _fetchPatientFromParams()
    {
        $request = $this->getRequest();
        $repo = $this->_em->getRepository('\Trendmed\Entity\Patient');

        if($request->getParam('id')) {
            $userId    = $request->getParam('id');
            $user      = $repo->find($userId);
            if(!$user) throw new \Exception('No patient found');
        } else {
            throw new \Exception('bad parameters given in '.__FUNCTION__);
        }
        return $user;
    }

    protected function _fetchClinicFromParams()
    {
        $request = $this->getRequest();
        $repo = $this->_em->getRepository('\Trendmed\Entity\Clinic');

        if($request->getParam('id')) {
            $userId    = $request->getParam('id');
            $user      = $repo->find($userId);
            if(!$user) throw new \Exception('No patient found');
        } else {
            throw new \Exception('bad parameters given in '.__FUNCTION__);
        }
        return $user;
    }
}
?>