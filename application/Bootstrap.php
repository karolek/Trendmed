<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initSession()
    {
        \Zend_Session::start();
    }

    public function _initAutoloaderNamespaces()
    {
        require_once APPLICATION_PATH . '/../vendor/autoload.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');

    }
    protected function _initLocale()
    {
        try {
            $locale = new Zend_Locale('auto');
        } catch (Zend_Locale_Exception $e) {
            $locale = new Zend_Locale('en_GB');
        }

        Zend_Registry::set('locale', $locale);
    }
    
    protected function _initTranslate()
    {
        $this->bootstrap('locale');
        $translate = new Zend_Translate(
                        array(
                            'adapter' => 'csv',
                            'content' => APPLICATION_PATH . '/../data/languages/en_GB.csv',
                            'locale' => 'en'
                        )
        );
        $translate->addTranslation(
            array(
                'content' => APPLICATION_PATH . '/../data/languages/pl_PL.csv',
                'locale' => 'pl'
            )
        );

        $locale = Zend_Registry::get('locale');
        $translate->setLocale($locale->getLanguage());

        Zend_Registry::set('Zend_Translate', $translate);
    }
    protected function _initTwitterBootstrap() {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->headLink()->appendStylesheet('/css/bootstrap.css');
        $view->headLink()->appendStylesheet('/css/trendmed.css');
        $view->headLink()->appendStylesheet('/css/bootstrap-responsive.css');
        $view->headLink()->appendStylesheet('/css/lightbox.css');
        $view->headLink()->appendStylesheet('/css/datepicker.css');

        $view->headScript()->appendFile('/js/bootstrap.min.js', $type = 'text/javascript');
        $view->headScript()->appendFile('/js/lightbox.js', $type = 'text/javascript');
        $view->headScript()->appendFile('/js/bootstrap-datepicker.js', $type = 'text/javascript');
        $view->headScript()->appendFile('/js/general.js', $type = 'text/javascript');
    }
    
    protected function _initViewHelpersPaths()
	{
        $this->bootstrap('view');
        $view = $this->getResource('view');
        // saveing view to registry
        \Zend_Registry::set('view', $view);
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Noumenal_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Trendmed_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/../library/Trendmed/view/helpers', 'Trendmed_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Br_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/modules/user/views/helpers', 'Br_View_Helper');
        $view->addHelperPath(APPLICATION_PATH . '/../library/Me/User/View/Helpers',
                'Me_User_View_Helpers_');

        # adding scripts path to layouts and paritals dir
        $view->addScriptPath(APPLICATION_PATH . '/layouts/scripts');

	}
	
	public function _initLogger()
	{
	   $this->bootstrap('log');
	   if(!$this->hasResource('log')) {
	       return false;
	   } 
	   Zend_Registry::set('log', $this->getResource('log'));
	}
	
	public function _initConfig()
	{
	   $env = APPLICATION_ENV;
	   $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',
                                     $env);
	   Zend_Registry::set('config', $config);       
	}
    
    /**
     * init jquery view helper, enable jquery, jqueryui, jquery ui css
     */
    protected function _initJquery() {
        $this->bootstrap('view');
        $this->bootstrap('config');
        $view = $this->getResource('view'); //get the view object
        //add the jquery view helper path into your project
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        //jquery lib includes here (default loads from google CDN)
        $view->jQuery()->enable()->setVersion('1.7.2'); //jQuery version, automatically 1.5 = 1.5.latest
        $config = \Zend_Registry::get('config');
        if($config->jquery->distribution->local == true) {
            $view->jQuery()->enable()->setLocalPath('/js/jquery-1.7.1.min.js');
        }
    }
    
    protected function _initHeadMeta()
    {
        $this->bootstrap('config');
        $config = \Zend_Registry::get('config');

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->headMeta()->appendHttpEquiv('Content-Type',
            'text/html; charset=UTF-8')
            ->appendHttpEquiv('Content-Language', 'en-GB');

        $view->headTitle($view->translate($config->site->headTitle));
        $view->headTitle()->setSeparator(' / ');

        // adding noindex, no fallow to all non-production instances
        if(APPLICATION_ENV != 'production') {
            $view->headMeta()->appendName('nofallow,noindex', 'robots');
        }
    }
    
    protected function _initMail()
    {
        $this->bootstrap('config');
        $config = \Zend_Registry::get('config');
        if ($config->mail->smtp->enable == true) {
           $tr = new Zend_Mail_Transport_Smtp($config->mail->smtp->host,
                   $config->mail->smtp->params->toArray());
           Zend_Mail::setDefaultTransport($tr); 
        }
    }

    public function _initDoctype()
    {
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype('HTML5');
    }

    public function _initAcl()
    {
        $this->bootstrap('config');
        $config = \Zend_Registry::get('config');
        if ($config->acl->use == TRUE) {
            /** Creating the ACL object */
            require_once 'Zend/Acl.php';
            $myAcl = new Zend_Acl();

            /** Creating Roles */
            require_once 'Zend/Acl/Role.php';
            $myAcl->addRole(new Zend_Acl_Role('guest'))
                ->addRole(new Zend_Acl_Role('clinic'), 'guest')
                ->addRole(new Zend_Acl_Role('patient'), 'guest')
                ->addRole(new Zend_Acl_Role('admin'), 'guest')
                ->addRole(new Zend_Acl_Role('god'), 'admin');

            /** Creating resources */
            require_once 'Zend/Acl/Resource.php';
            $myAcl->addResource(new Zend_Acl_Resource('mvc:admin'))
            ->addResource(new Zend_Acl_Resource('mvc:admin.index', 'mvc:admin'))
            ->addResource(new Zend_Acl_Resource('mvc:catalog'))
            ->addResource(new Zend_Acl_Resource('mvc:catalog.reservations'))
            ->addResource(new Zend_Acl_Resource('mvc:patient.reservations'))
            ->addResource(new Zend_Acl_Resource('mvc:clinic'))
            ->addResource(new Zend_Acl_Resource('mvc:clinic.index', 'mvc:clinic'))
            ->addResource(new Zend_Acl_Resource('mvc:clinic.public', 'mvc:clinic'))
            ->addResource(new Zend_Acl_Resource('mvc:clinic.register', 'mvc:clinic'))
            ->addResource(new Zend_Acl_Resource('mvc:patient'))
            ->addResource(new Zend_Acl_Resource('mvc:patient.index', 'mvc:patient'))
            ->addResource(new Zend_Acl_Resource('mvc:patient.public', 'mvc:patient'))
            ->addResource(new Zend_Acl_Resource('mvc:patient.register', 'mvc:patient'));

            /** Creating permissions */
            $myAcl
                # admin panel rights #
                ->deny('guest', 'mvc:admin')
                ->allow('admin', 'mvc:admin')
                ->allow('guest', 'mvc:admin.index', 'index')

                # catalog rights #
                ->allow('guest', 'mvc:catalog')
                ->deny('guest', 'mvc:catalog.reservations', array('new'))
                ->allow('guest', 'mvc:catalog.reservations')
                ->allow('patient', 'mvc:catalog.reservations', array('new'))

                # clinic panel rights
                ->allow('clinic', 'mvc:clinic')
                ->deny('guest', 'mvc:clinic')
                ->allow('guest', 'mvc:clinic.index', array('index', 'password-recovery', 'new-password-from-token'))
                ->allow('guest', 'mvc:clinic.public')
                ->allow('guest', 'mvc:clinic.register')


                # patient reservation only for patient
                ->deny('guest', 'mvc:patient.reservations')
                ->allow('patient', 'mvc:patient.reservations')

                #patient panel rights
                ->allow('patient', 'mvc:patient')
                ->deny('guest', 'mvc:patient')
                ->allow('guest', 'mvc:patient.index', array('index', 'password-recovery', 'new-password-from-token'))
                ->allow('guest', 'mvc:patient.public')
                ->allow('guest', 'mvc:patient.register');


            /** Getting the user role */
            $auth = \Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $user = $auth->getIdentity();
                $role = $user['roleName'];
            } else {
                $role = 'guest';
            }

            if (empty($role)) throw new \Exception('No role setup for user in this request. Cant proceed with ACL');

            $fc = Zend_Controller_Front::getInstance();
            $fc->registerPlugin(new \Me_Controller_Plugin_Acl($myAcl, $role));
        }
    }

}
