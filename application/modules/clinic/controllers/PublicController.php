<?php
/**
 * Controller for public (displayed to non clinic users) information's and actions
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_PublicController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em = $this->_helper->getEm();

    }

    public function profileAction()
    {
        $request = $this->getRequest();
        $this->_helper->_layout->setLayout('homepage');

        $slug = $request->getParam('slug');
        if (!$slug) {
            throw new \Exception('No slug in public profile', 404);
        }

        $clinic = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findOneBySlug($slug);

        if (!$clinic) throw new \Exception('No clinic by the slug of ' . $slug . ' found', 404);

        # adding new visist to clinic
        if (!$_COOKIE['visit_'.$clinic->id]) {
           setcookie('visit_'.$clinic->id, true, time() + 24*3600, '/');
            $clinic->addView();
            $this->_em->persist($clinic);
            $this->_em->flush();
        }

        $this->view->headTitle($clinic->name);
        $this->view->clinic = $clinic;
    }

    // search for given clinc by it's name or city
    public function searchAction()
    {
        $request = $this->getRequest();

        $search = $request->getParam('search', null);

        if ($search) { // do the search (not the ska :-)
            $repository = $this->_em->getRepository('\Trendmed\Entity\Clinic');
            $clinics = $repository->findByNameOrCity($search, $search);
            $this->view->clinics = $clinics;
            $this->view->search = $search; // to add to the form
        }

        $this->view->headTitle($this->view->translate('Search for institution'));
        $this->_helper->_layout->setLayout('homepage');

    }

}
