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
                if ($values['question']) {
                    $question = $reservation->getQuestion();
                    # append any new text if any to question field
                    $reservation->setQuestion($values['question']. "\n\n" . $question);
                }

                $reservation->setStatus('confirmed');
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
                if ($values['question']) {
                    $question = $reservation->getQuestion();
                    # append any new text if any to question field
                    $reservation->setQuestion($values['question']. "\n\n" . $question);
                }

                $reservation->setStatus('closed');
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

    /**
     * This is for paying the bill if clinic want's it
     * Using paypal
     */
    public function payBillAction()
    {
        $reservation = $this->_getReservationFromParams();

        # checking if clinic want's the bill
        if ($reservation->billStatus == $reservation::BILL_STATUS_PAID) {
            throw new \Exception('Reservation paid or clinic does not require payment');
        }

        if ($reservation->getStatus() != 'confirmed') {
            throw new \Exception('Only confirmed reservation needs payment');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            # user want to make payment
            # would be good to make na Payment object with information about paymets: date, amount, who do it, status etc
        }
        $this->view->headTitle('Payment for service reservation');

    }

    /**
     * verifies that payment has been done and setups reservation as paid
     */
    public function billPaidAction()
    {
        $reservation = $this->_getReservationFromParams();

        # checking if clinic want's the bill
        if ($reservation->billStatus == $reservation::BILL_STATUS_PAID) {
            throw new \Exception('Reservation paid or clinic does not require payment');
        }

        $request = $this->getRequest();
        ## TODO: some verification would be nice of incominng payment
        $reservation->setBillStatus($reservation::BILL_STATUS_PAID);
        $this->_em->persist($reservation);
        $this->_em->flush();

        $this->view->headTitle('Payment confirmation');
        $this->view->reservation = $reservation;
    }

    public function rateReservationAction()
    {
        $reservation = $this->_getReservationFromParams();
        $request     = $this->getRequest();

        # checking if reservation has got a rateing allready
        if ($reservation->rating) {
            $this->_helper->FlashMessenger(array('warning' => 'This reservation was already rated by you.'));
            $this->_helper->Redirector('index', 'profile');
        }

        # checking if reservation was in past
        if ($reservation->getDateTo() > new \DateTime()) {
            $this->_helper->FlashMessenger(array('warning' => 'Date range of this reservation must past, before the
                reservation can be rated.'));
            #$this->_helper->Redirector('index', 'profile');
        }

        # survey form
        $form = new \Patient_Form_ReservationSurvey();

        # adding reservation
        if ($request->isPost()) {

            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $rating = new \Trendmed\Entity\Rating();
                $rating->setPriceRate($values['priceRate']);
                $rating->setServiceRate($values['serviceRate']);
                $rating->setStuffRate($values['stuffRate']);
                $rating->setComment($values['comment']);
                $reservation->setRating($rating);
                $this->_em->persist($rating);
                $this->_em->persist($reservation);
                # recount clinic rating and persist it
                $reservation->clinic->recountRating();
                $this->_em->persist($reservation->clinic);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array(
                    'success' => 'Reservation has been rated. Thank you for your opinion.'
                ));
                $this->_helper->Redirector('index', 'profile');

            }
        }

        $this->view->headTitle($this->view->translate('Reservation survey'));
        $this->view->reservation = $reservation;
        $this->view->form = $form;
    }

}

