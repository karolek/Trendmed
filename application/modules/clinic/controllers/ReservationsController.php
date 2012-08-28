<?php
/**
 * This controller takes care of login/logout action for clinic.
 * It also has some nice actions for password recovery.
 * Please refer to parent class for more info about the functions.
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
class Clinic_ReservationsController extends Me_User_Controllers_LoginController
{

    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
    }

    public function confirmAction()
    {
        $reservation = $this->_getReservationFromParams();
        $request = $this->getRequest();

        # reservation anwser form
        $form = new \Clinic_Form_ReservationAnwser();

        # setting up a label for submit button
        $form->getElement('submit')->setLabel('Potwierdź');

        # setting action param of the form
        $form->setAction();

        if ($request->isPost()) {
            # clinic want to confirm this reservation
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $reservation->setStatus('confirmed');
                $reservation->setAnswer($values['anwser']);

                $this->_em->persist($reservation);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Rezerwacja potwierdzona'));
                # forward to reservations list
                $this->_helper->Redirector('index', 'profile', 'clinic');
            }
        }
        $this->view->form = $form;
        $this->view->reservation = $reservation;
        $this->view->headTitle('Potwierdzenie rezerwacji');
    }

    /**
     * Fetches and validates reservation given in "id" parameter in URL
     *
     * @return \Trendmed\Entity\Reservation
     * @throws Exception
     */
    protected function _getReservationFromParams()
    {
        $request = $this->getRequest();
        $reservationId = $request->getParam('id', NULL);
        if (NULL == $reservationId) throw new \Exception('No reservation id given in '.__FUNCTION__);
        $reservation = $this->_em->find('\Trendmed\Entity\Reservation', $reservationId);
        # no reservation found by this id
        if (!$reservation) throw new \Exception('No reservation with id ' . $reservationId .' found');
        # checking if current logged clinic is the owner of this reservation
        if ($this->_helper->LoggedUser()->id != $reservation->clinic->id) {
            throw new \Exception('The reservation You are trying to view is not yours');
        }
        # ok, everything is fine, if you need more validation of reservation add it here
        return $reservation;
    }
}

