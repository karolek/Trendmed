<?php
/**
 * This controller takes care of login/logout action for clinic.
 * It also has some nice actions for password recovery.
 * Please refer to parent class for more info about the functions.
 * 
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
class Patient_ReservationsController extends Me_User_Controllers_LoginController
{

    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
        $this->view->config = \Zend_Registry::get('config');

    }

    public function confirmNewDateAction()
    {
        $reservation = $this->_getReservationFromParams();
        $request = $this->getRequest();

        # reservation anwser form
        $form = new \Patient_Form_ReservationAnwser();
        $form->getElement('question')->setDescription('You can type in any additional comment to clinic');

        # setting up a label for submit button
        $form->getElement('submit')->setLabel('Confirm reservation with new date');


        if ($request->isPost()) {
            # clinic want to confirm this reservation
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $reservation->setStatus('confirmed');
                if ($values['question']) {
                    $question = $reservation->getQuestion();
                    # append any new text if any to question field
                    $reservation->setQuestion($values['question']. "\n\n" . $question);
                }

                $this->_em->persist($reservation);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Reservation confirmed'));
                # forward to reservations list
                $this->_helper->Redirector('index', 'profile', 'patient');
            }
        }
        $this->view->form = $form;
        $this->view->reservation = $reservation;
        $this->view->headTitle('New date of reservation confirmation');
    }

    public function cancelAction()
    {
        $reservation = $this->_getReservationFromParams();
        $request = $this->getRequest();

        # reservation anwser form
        $form = new \Patient_Form_ReservationAnwser();
        $form->getElement('question')->setDescription('You can type in any additional comment to clinic, like reason for
         cancellation');

        # setting up a label for submit button
        $form->getElement('submit')->setLabel('Cancel reservation');


        if ($request->isPost()) {
            # clinic want to confirm this reservation
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $reservation->setStatus('closed');
                if ($values['question']) {
                    $question = $reservation->getQuestion();
                    # append any new text if any to question field
                    $reservation->setQuestion($values['question']. "\n\n" . $question);
                }

                $this->_em->persist($reservation);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => 'Reservation canceled'));
                # forward to reservations list
                $this->_helper->Redirector('index', 'profile', 'patient');
            }
        }
        $this->view->form = $form;
        $this->view->reservation = $reservation;
        $this->view->headTitle('Reservation cancellation');
    }

    public function viewAction()
    {
        $reservation = $this->_getReservationFromParams();
        $this->view->reservation = $reservation;

        $this->view->headTitle('Details of reservation #'.$reservation->id);
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
        if ($this->_helper->LoggedUser()->id != $reservation->patient->id) {
            throw new \Exception('The reservation You are trying to view is not yours');
        }
        # ok, everything is fine, if you need more validation of reservation add it here
        return $reservation;
    }

    public function getPdfAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $reservation = $this->_getReservationFromParams();
        # needed for translate inside reservation object
        $reservation->setView($this->view);

        $fpdf = $reservation->getPDF();
        $fpdf->Output("trendmed_reservation.pdf", "D");
    }

    public function payBillAction()
    {
        $reservation = $this->_getReservationFromParams();

    }

}

