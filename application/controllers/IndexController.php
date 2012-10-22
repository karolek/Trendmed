<?php

class IndexController extends \Zend_Controller_Action
{

    protected $_em;
    
    public function init()
    {
        $this->_em = $this->_helper->getEm();
        /* Initialize action controller here */
    }

    public function adRedirectAction()
    {
        $req = $this->getRequest();
        $adId = $req->getParam('id', null);
        // I will use adId as filename to hash the id in URL
        $repo = $this->_em->getRepository('\Trendmed\Entity\BannerAd');
        $ad = $repo->findOneByFile($adId);
        if(!$ad) throw new \Exception('Cant find ad with filename :'.$adId);

        $ad->clickCount++;
        $this->_em->persist($ad);
        $this->_em->flush();
        $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $r->setCode(301);
        $r->gotoUrl($ad->target)->redirectAndExit();
    }

    public function indexAction()
    {
        // action body
        $this->view->headTitle('Homepage');
        $this->_helper->layout()->setLayout('homepage');

        // fetching latest articles for home page
        $this->view->articles = $this->_em->getRepository('\Trendmed\Entity\Page')
            ->fetchLatestArticles(3);

        # fetching latest clinics for home page
        $this->view->newClinics = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->fetchLatestClinics(3);

        # fetching popular services for home page
        $this->view->newServices = $this->_em->getRepository('\Trendmed\Entity\Service')
            ->fetchLatestServices(3);

        # fetching popular clinics
        $this->view->popularClinics = $this->_em->getRepository('\Trendmed\Entity\Clinic')
            ->findMostPopular(3);
    }

    /**
     * Return some information about request category
     */
    public function getCategoriesAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $parentId = $request->getParam('parentId');
            $repo = $this->_em->getRepository('\Trendmed\Entity\Category');
            $subcategories = $repo->findForParentAsArray($parentId);
            $json = \Zend_Json::encode($subcategories);
            echo $json;
        } else {
            throw new \Exception('Invalid request type in '.__FUNCTION__);
        }
    }

    /**
     * Used by AJAX request to fetch sub categories for add new service.
     * Subcategories will be filtered by categories allready used by clinic
     */
    public function getSubcategoriesForClinicAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $parentId = $request->getParam('parentId');
            $repo = $this->_em->getRepository('\Trendmed\Entity\Category');
            $subcategories = $repo->findForParentAsArray($parentId, $this->_helper->LoggedUser()->usedCategories());
            $json = \Zend_Json::encode($subcategories);
            echo $json;
        } else {
            throw new \Exception('Invalid request type in '.__FUNCTION__);
        }
    }

    public function contactAction()
    {
        $request    = $this->getRequest();
        $type       = $this->getParam('type');
        if (!$type) throw new \Exception('invalid type parameter or none given in '.__FUNCTION__);

        $formClassName = 'Application_Form_'.ucfirst($type);

        $form = new $formClassName;

        # adding type field in hidden element in every form to pass on in request
        $form->addElement('hidden', 'type', array('value' => $type));

        # checking if user is logged, if yes then remove user information field (change them to hidden)
        if ($user = $this->_helper->LoggedUser()) {
            $form->getElement('email')->setValue($user->getEmailaddress());

        }

        if ($request->isPost()) {
            $log = \Zend_Registry::get('log');
            $config = \Zend_Registry::get('config');
            $post = $request->getPost();
            if ($form->isValid($post)) {

                $values = $form->getValues();

                # sending notification to clinic
                $mail = new \Zend_Mail('UTF-8');

                $message = $form->getMessageIntro();
                $message .="\n\n";

                foreach ($values as $valueName => $value) {
                    if ($valueName != 'type') {
                        $message .= $form->getElement($valueName)->getLabel().': '.$value."\n";
                    }
                }

                $mail->setBodyText($message);
                $mail->setFrom($values['email'], $config->siteEmail->fromName); // setting FROM values from config
                $mail->addTo($config->siteEmail->fromAddress, $config->siteEmail->fromName);
                $mail->setSubject($form->getSubject());
                $mail->send();
                $log->debug('E-mail send to admin (type: '.$type.')');
                $this->_helper->FlashMessenger(array('success' => 'Message send. Thanks for shearing.'));

                // clearing the message field
                $form->getElement('categoryName')->setValue("");

                // redirecting to panel if newcategory was suggested
                if ($type == 'newcategory' and $this->_helper->LoggedUser()->roleName == 'clinic') {
                    $this->redirect($this->view->url(array(
                        'action'        => 'add-service',
                        'controller'    => 'services',
                        'module'        => 'clinic'
                    ), 'default', true));
                } else {
                    // redirecting to HP
                    $this->redirect($this->view->url(array(
                        'action' => 'index',
                        'controller' => 'index',
                        'module' => 'default'
                    ), 'default'));
                }
            } else {
                $this->_helper->FlashMessenger(array('warning' => 'Please fix the errors in the form.'));
            }
        }

        $this->view->headTitle($this->view->translate('Contact Trendmed.eu'));
        $this->view->form = $form;
    }

    public function shareLinkAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $post = $request->getPost();
            $myEmail = $post['myEmail'];
            // geting clinic to recomendation
            $clinic = $this->_em->find('\Trendmed\Entity\Clinic', $post['clinic_id']);
            if (!$clinic) {
                throw new \Exception('Given clinic with id '.$post['clinic_id']. ' not found');
            }

            // list of emails addres to send email to
            $list = explode(',', trim($post['shareEmail']));
            $i = 0;
            foreach($list as $email) {
                $clinic->recommendedBy($myEmail, $email);
                $i++;
            }

            echo $this->view->translate('Recommendation send! Thank you!');

        } else {
            throw new \Exception('Request should be POST in '.__FUNCTION__);
        }
    }

    public function changeLanguageAction()
    {
        $request = $this->getRequest();
        $newLang = $request->getParam('lang');

        $session = new Zend_Session_Namespace('selectedLanguage');
        $session->currentLanguage = $newLang;
        $this->_helper->Redirector('index','index', 'default');
    }
}

