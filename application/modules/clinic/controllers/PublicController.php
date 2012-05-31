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
        $this->_em =  $this->_helper->getEm();

    }

    public function profileAction()
    {
        $request = $this->getRequest();
        $slug = $request->getParam('slug');
        if(!$slug) {
            throw new \Exception('No slug in public profile', 404);
        }

        $clinic = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findOneBySlug($slug);

        if(!$clinic) throw new \Exception('No clinic by the slug of '.$slug.' found', 404);
        $this->view->headTitle($clinic->name);
        $this->view->clinic = $clinic;
    }

}
