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

        # populating the form with services of a given clinic

        if ($request->isPost()) {
            #new reservation POST request
            $post = $request->getPost();
            # validating if atleast one service is selected
            if(count($post['services']) < 1) {
                $form->getElement('services')->addError('At least one service must be select');
            }

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
                $this->_em->persist($reservation);
                $this->_em->flush();
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

        $form->populateServicesFromClinic($clinic);

        #passing form to view
        $this->view->clinic = $clinic;
        $this->view->form = $form;

        #Seetting up a view title
        $this->view->headTitle($this->view->translate('New reservation'));
    }

}

