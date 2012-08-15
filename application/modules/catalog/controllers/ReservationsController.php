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
        $slug       = $request->getParam('slug');
        if (!$slug) {
            throw new \Exception('No clinic slug param given');
        }

        # searching for clinic
        $clinic = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findOneBySlug($slug);

        if (!$clinic) {
            throw new \Exception(
                'No clinic by slug: '.$slug.' found in the system'
            );
        }

        if ($request->isPost()) {
            #new reservation POST request
            $post = $request->getPost();
        } else {

        }
        #passing form to view
        $this->clinic = $clinic;
        $this->view->form = $form;
    }

}

