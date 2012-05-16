<?php
/**
 * Clinic profile controller. Handles profile editing of clinic, 
 * managing services, account data and so on
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_ServicesController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
        $this->view->headTitle('Twoje usługi');
    }

    /** 
     * Dashboard for logged clinic. Shows latest reservations and infos.
     */
    public function indexAction()
    {
        $this->view->services = $this->_helper->LoggedUser()->services;
    }


    /**
     * Place to edit, add, modify clinics services
     *
     * @throws \Exception
     */
    public function manageServicesAction()
    {
        $request = $this->getRequest();
        $form = new Clinic_Form_Service();
        $id = $request->getParam('id', null);
        $config = \Zend_Registry::get('config');
        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');


        if ($id) { //edit
            $service = $this->_em->find('\Trendmed\Entity\Service', $id);
            if (!$service) throw new \Exception('Bad parameters');
            $form->populate(array('id' => $service->getId()));
            $form->populate($service->toArray());
            $this->view->headTitle('Edycja usługi');
        } else { // new
            $service = new \Trendmed\Entity\Service();
            $this->view->headTitle('Dodawanie usługi');
        }


        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $service->setOptions($values);

                foreach ($config->languages as $lang) {
                    if (!$lang->default) {
                        //translations
                        $repository->translate(
                            $service, 'description', $lang->code,
                            $values['description_'.$lang->code]
                        );
                    }
                }
                $service->setClinic($this->_helper->LoggedUser());
                $service->setCategory($this->_em->find('\Trendmed\Entity\Category', $values['categories']));
                $this->_em->persist($service);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'Changes saved'));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('error' => 'Please correct the form'));
            }
        }

        // we must remove from the categories of service, allready added categories
        $result = $form->getElement('categories')->getMultiOptions();

        foreach ($this->_helper->LoggedUser()->services as $clinicServices) {
            foreach($result as &$optgroup) {
                if(isset($optgroup[$clinicServices->category->id]) and
                    $clinicServices->category->id != $service->getCategory()->getId()) {
                    unset($optgroup[$clinicServices->category->id]);
                }

            }
        }
        $form->getElement('categories')->setMultiOptions($result);

        $this->view->form = $form;
        $this->_helper->EnableCke($this->view);
    }
    

}
