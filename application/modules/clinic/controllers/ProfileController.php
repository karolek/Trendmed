<?php
/**
 * Clinic profile controller. Handles profile editing of clinic, 
 * managing services, account data and so on
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_ProfileController extends Zend_Controller_Action
{
    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();

    }

    /** 
     * Dashboard for logged clinic. Shows latest reservations and infos.
     */
    public function indexAction()
    {
    
    }

    /**
     * Edits this part of clinics description that is visible to patients
     * like logo, pictures, description
     * @throws Exception if no logged user in the system
     */
    public function editProfileAction()
    {
        $request = $this->getRequest();

        $this->_helper->EnableCke($this->view, 'basic');

        $this->view->headTitle('Clinic profile');
        $form = new Clinic_Form_ClinicProfile_MultiLangDesc();
        $form->setAction($this->_helper->url('edit-profile'));
        $form->addElement(new \Zend_Form_Element_Submit('Zapisz'));

        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new Exception(
                'No logged user found, so now edit Details is possible'
            );
        }

        // populate form
        $config = \Zend_Registry::get('config');
        $form->populateFromUser($user);

        // init the form for new photo
        $photoForm = new Clinic_Form_ClinicPhoto();
        $photoForm->setAction($this->view->url(
            array(
                'action' => 'add-photo'
            )
        ));
        $this->view->photoForm = $photoForm;

        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                foreach ($config->languages as $lang) {
                    if ($lang->default == 1) { // we must add default values to our main entity
                        $user->setCustomPromos(
                            $values['customPromos_'.$lang->code]);
                        $user->setDescription(
                            $values['description_'.$lang->code]
                        );
                    } else {
                        $repository->translate(
                            $user, 'customPromos', $lang->code,
                            $values['customPromos_'.$lang->code]
                        );
                        $repository->translate(
                            $user, 'description', $lang->code,
                            $values['description_'.$lang->code]
                        );
                    }
                }
                $this->_em->persist($user);
                $this->_em->flush();

                $this->_helper->FlashMessenger(
                    array('success' => 'You have changed Your details')
                );
                $this->_helper->Redirector(
                    'profile', 'public', 'clinic', array('slug' => $user->getSlug())
                );
            }
        }
        $this->view->form = $form;
     }

    public function addPhotoAction()
    {
        $request = $this->getRequest();
        $clinic = $this->_helper->LoggedUser();
        if($request->isPost()) {
            $photo = new \Trendmed\Entity\ClinicPhoto();
            $clinic->addPhoto($photo);

            // doing all the upload magic
            $photo->processFile();

            $this->_em->persist($photo);
            $this->_em->persist($clinic);
            $this->_em->flush();

            if($request->isXmlHttpRequest()) {
                echo 'OK';
                $this->_helper->layout()->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $this->_helper->FlashMessenger(array(
                    'success' => 'New photo added'
                ));
                $this->_helper->Redirector(
                    'edit-profile'
                );
            }
        } else {
            throw new \Exception('Add photo should be a post request');
        }
    }
    /**
     * Edit those parts of the clinic description that are use for 
     * administration purpuses, like bank account no., 
     * want bill setting, rep email or address.
     */
    public function editAccountAction()
    {


    }

    /**
     * Action for changing password of clinic account
     *
     * @throws \Exception
     */
    public function changePasswordAction() 
    {
        $this->view->headTitle($this->view->translate('Password change'));
        $form = new Clinic_Form_NewPassword();

        $user = $this->_helper->LoggedUser();
        if (!$user) {
            throw new \Exception(
                'No logged user found, so now edit Details is possible'
            );
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                $user->setPassword($values['password']);
                $user->sendNewPasswordEmail();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger(
                    array('success' => 'You have changed Your password')
                );
            }
        }
        $this->view->form = $form;
    }

    /**
     * Editing clinic public logo
     *
     */
    public function editLogoAction()
    {
        $this->view->headTitle($this->view->translate('Edit my logo'));
        $this->view->user = $user = $this->_helper->LoggedUser();

        $request = $this->getRequest();
        $form = new Clinic_Form_ClinicProfile_Logo();

        if($request->isPost()) {
            $post = $request->getPost();
            if($form->isValid($post)) {
                $user->processLogo();
                $this->_em->persist($user);
                $this->_em->flush();
                $this->_helper->FlashMessenger('Logo uploaded.');
                $this->_helper->Redirector('edit-logo');
            } else {
                $this->_helper->FlashMessenger('Fix errors with the file');
            }
        }
        $this->view->form = $form;
    }

    public function deleteLogoAction()
    {
        $user = $this->_helper->LoggedUser()->deleteLogo();
        $this->_em->persist($user);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'Your logo has been deleted'));
        $this->_helper->Redirector('edit-logo');
    }
}
