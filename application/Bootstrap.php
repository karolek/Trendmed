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
        $locale = new Zend_Locale();
        Zend_Registry::set('locale', $locale);
    }
    
    protected function _initTranslate()
    {
        $this->bootstrap('locale');
        $translate = new Zend_Translate(
                        array(
                            'adapter' => 'csv',
                            'content' => APPLICATION_PATH . '/../data/languages',
                        )
        );
        Zend_Registry::set('Zend_Translate', $translate);
    }
    protected function _initTwitterBootstrap() {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->headLink()->appendStylesheet('/css/bootstrap.css');
        $view->headLink()->appendStylesheet('/css/trendmed.css');
        $view->headLink()->appendStylesheet('/css/bootstrap-responsive.css');
        $view->headLink()->appendStylesheet('/css/datepicker.css');
        
        $view->headScript()->appendFile('/js/bootstrap.min.js', $type = 'text/javascript');
        $view->headScript()->appendFile('/js/bootstrap-datepicker.js', $type = 'text/javascript');   
        $view->headScript()->appendFile('/js/general.js', $type = 'text/javascript');
    }
    
    protected function _initViewHelpersPaths()
	{
        $this->bootstrap('view');
        $view = $this->getResource('view');
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Noumenal_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Trendmed_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Br_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . '/modules/user/views/helpers', 'Br_View_Helper');
        		$view->addHelperPath(APPLICATION_PATH . '/../library/Me/User/View/helpers',
                'Me_User_View_Helpers_');

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
            $view->jQuery()->enable()->setLocalPath('js/jquery-1.7.1.min.js');
        }
    }
    
    protected function _initHeadMeta()
    {
         $this->bootstrap('view');
         $view = $this->getResource('view');
         $view->headMeta()->appendHttpEquiv('Content-Type',
 		                                   'text/html; charset=UTF-8')
 		                 ->appendHttpEquiv('Content-Language', 'en-GB');
 		$view->headTitle('Trendmed');
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
            ->addResource(new Zend_Acl_Resource('mvc:admin.index', 'mvc:admin'));

            /** Creating permissions */
            $myAcl->deny('guest', 'mvc:admin')
                ->allow('admin', 'mvc:admin')
                ->allow('guest', 'mvc:admin.index', 'index');

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
