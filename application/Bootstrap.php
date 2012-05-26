<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
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
        $view = $this->getResource('view'); //get the view object
        //add the jquery view helper path into your project
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        //jquery lib includes here (default loads from google CDN)
        $view->jQuery()->enable()->setVersion('1.7.2'); //jQuery version, automatically 1.5 = 1.5.latest
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
}
