<?php
/**
 * Controller takes care of creating new reservations and managing them from user point of view
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
use Doctrine\ORM\Tools\Pagination\Paginator;

class Catalog_ReservationsController extends \Zend_Controller_Action
{

    protected $_em; //entity manager
    protected $_repo; // categories repository, for use of use

    public function init()
    {
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Category');
    }

    /**
     * Making of a new reservation, displays a form with reservation options
     */
    public function newAction()
    {
        $request    = $this->getRequest();
        $form       = new \Catalog_Form_Reservation();
        # setting up a view script helper on this form
        $form->setDecorators(array(array('ViewScript' ,array(
            'viewScript' => 'reservations/_reservationForm.phtml'
        ))));

        $slug       = $request->getParam('slug');
        if (!$slug) {
            throw new \Exception('No clinic slug param given');
        }

        # searching for clinic
        $clinic = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findOneBySlug($slug);

        if (!$clinic->id) {
            throw new \Exception(
                'No clinic by slug: '.$slug.' found in the system'
            );
        }



        // saveing reservation to session temporary if patient profile is not filled
        if ($this->_helper->LoggedUser()->isProfileFilled() != 1) {
            $reservationInSession = new Zend_Session_Namespace('Reservation_Temp');
            $reservationInSession->clinic_id = $clinic->id;
            $reservationInSession->clinic_slug = $clinic->slug;

            $reservationInSession->setExpirationHops(6);
        }


        # populating the form with services of a given clinic

        if ($request->isPost()) {
            #new reservation POST request
            $post = $request->getPost();
            # checking if this a pre request from other page like clinic page with pre selected services
            # or user acctualy want to make a reservation
            if($post['pre_services']) {
                # user just pre selects services
                $form->populate(array(
                    'services' => $post['pre_services']
                ));
            } else {
                # validating if atleast one service is selected
                if($this->_helper->LoggedUser()->isProfileFilled() <1 ) {
                    $form->addError('You have to fill Your profile with Your personal data before making a reservation.');
                    $form->populate($post);
                    # saveing to session pre selected services and anything from temp
                    foreach ($post as $key => $value) {
                        $reservationInSession->$key = $value;
                    }

                }
                if(count($post['services']) < 1) {
                    $form->getElement('services')->addError('At least one service must be select');
                } else {
                    if ($form->isValid($post)) {
                        # double checking if user is logged, but should be controller by ACL
                        if (!$this->_helper->LoggedUser()) {
                            throw new Exception('Only logged patients can make reservations');
                        }
                        $reservation = new \Trendmed\Entity\Reservation();
                        $values = $form->getValues();
                        #mapping of a post array to new reservation, this is so lame, should auto somehow
                        $reservation->question  = $values['question'];
                        $reservation->patient   = $this->_helper->LoggedUser();
                        $reservation->dateFrom  = new \DateTime($values['dateFrom']);
                        $reservation->dateTo    = new \DateTime($values['dateTo']);
                        foreach($values['services'] as $serviceId) {
                            $reservation->addService($this->_em->find('\Trendmed\Entity\Service', $serviceId));
                        }
                        $clinic = $reservation->services[0]->clinic;
                        # checkign if clinic want's bill
                        if ($clinic->wantBill === false) {
                           $reservation->setBillStatus(\Trendmed\Entity\Reservation::BILL_STATUS_NOT_WANTED);
                        }
                        $reservation->clinic = $clinic;

                        $this->_em->persist($clinic);
                        $this->_em->persist($reservation);
                        $this->_em->flush();

                        # sending confirmation about new reservation to clinic and patient
                        $reservation->sendStatusNotification('new');

                        # clearing session
                        Zend_Session::namespaceUnset('Reservation_Temp');

                        $this->_helper->FlashMessenger(array(
                            'success' => 'Reservation booked'
                        ));
                        $this->_helper->Redirector('index', 'profile', 'patient');
                    } else {
                        $this->_helper->FlashMessenger(array(
                            'warning' => 'Please fix the errors in form'
                        ));
                    }
                }
            }

        } else {
            // if not post than maybe this is a redirect from edit profile details page
            // and I have to filling from session to make on form
        }
        $form->populateServicesFromClinic($clinic);


        #passing form to view
        $this->view->clinic = $clinic;
        $this->view->form = $form;

        #Seetting up a view title
        $this->view->headTitle($this->view->translate('New reservation'));

        $this->_helper->_layout->setLayout('homepage');

    }

}

