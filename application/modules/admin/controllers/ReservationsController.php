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
        $session = new Zend_Session_Namespace('admin_reservations');
        $session->redirect = $_SERVER['REQUEST_URI'];
    }

    public function clinicReservationsAction()
    {
        $this->view->headTitle('Rezerwacje kliniki');
        $this->view->clinic = $this->_fetchClinicFromParams();
        $session = new Zend_Session_Namespace('admin_reservations');
        $session->redirect = $_SERVER['REQUEST_URI'];
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


    protected function _fetchReservationFromParams()
    {
        $request = $this->getRequest();
        $repo = $this->_em->getRepository('\Trendmed\Entity\Reservation');

        if($request->getParam('id')) {
            $id             = $request->getParam('id');
            $entity         = $repo->find($id);
            if(!$entity) throw new \Exception('No reservation found');
        } else {
            throw new \Exception('bad parameters given in '.__FUNCTION__);
        }
        return $entity;
    }

    public function changePayBillStateAction()
    {
        $request = $this->getRequest();
        $newState = $request->getParam('new-state');

        $reservation = $this->_fetchReservationFromParams();

        switch($newState) {
            case 'paid':
                $reservation->setBillStatus(\Trendmed\Entity\Reservation::BILL_STATUS_PAID);
                $this->_helper->FlashMessenger(array(
                        'info' => 'Rezerwacja oznaczona jako opłacona'
                    )
                );
                break;
            case 'not-paid':
                $reservation->setBillStatus(\Trendmed\Entity\Reservation::BILL_STATUS_NOT_PAID);
                $this->_helper->FlashMessenger(array(
                        'info' => 'Rezerwacja oznaczona jako nieopłacona'
                    )
                );
                break;
            case 'not-wanted':
                $reservation->setBillStatus(\Trendmed\Entity\Reservation::BILL_STATUS_NOT_WANTED);
                $this->_helper->FlashMessenger(array(
                        'info' => 'Rezerwacja oznaczona jako niewymagana'
                    )
                );
                break;
            default:
                throw new \Exception('Undefined paybill state given in '.__FUNCTION__);
                break;
        }
        $this->_em->persist($reservation);
        $this->_em->flush();

        // where to redirect after?
        $session = new Zend_Session_Namespace('admin_reservations');
        if($session->redirect) {
            $url = $session->redirect;
            unset($session->redirect);
            $this->_redirect($url);
        } else {
            $this->_helper->Redirector('index', 'index');
        }
    }
}
?>