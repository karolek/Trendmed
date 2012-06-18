<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                $pageNotFound = true;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message.':'.$errors->exception->getMessage(), $priority, $errors->exception);
            $log->debug($errors->exception->getTrace());
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        // mail exceptions to services dept.
        if ($this->getInvokeArg('mailExceptions') == true AND $pageNotFound !== true) {
            $mail = new \Zend_Mail('UTF-8');
            $config = \Zend_Registry::get('config');
            $htmlBody = "Exception occurred on the system<br>";
            $htmlBody .= "<br>Message --------------------<br>";
            $htmlBody .= $errors->exception->getMessage();
            $htmlBody .= "<br>File -----------------------<br>";
            $htmlBody .= $errors->exception->getFile();
            $htmlBody .= "<br>Code -----------------------<br>";
            $htmlBody .= $errors->exception->getCode();
            $htmlBody .= date("d-m-Y H:i:s");
            $htmlBody .= "<br>Host -----------------------<br>";
            $htmlBody .= $_SERVER['REMOTE_HOST'];


            $mail->setBodyHtml($htmlBody);
            $mail->setFrom($config->siteEmail->fromAddress, 'IAA Exception handler');
            $mail->addTo($config->siteEmail->adminEmail, 'IAA admin');
            $mail->setSubject('Exception occurred on IAA project');
            $mail->send();
            $this->view->exception = $errors->exception;
        }

        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    public function deniedAction()
    {
    }
}

