<?php
use Doctrine\ORM\Tools\Pagination\Paginator;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClinicsController
 *
 * @author Bard
 */
class Admin_NewsletterController extends Zend_Controller_Action {

    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
        $this->view->headTitle('Zarządzanie newsletterem');
    }

    public function exportEmailsToCsvAction()
    {
        $request = $this->getRequest();

        if($request->isPost()) {
            $post = $request->getPost();
            $fp = fopen(APPLICATION_PATH . '/../public/csvTemp/export.csv', 'w');

            switch($post['type']) {
                case 'clinics':
                    $output = $this->_em->getRepository('\Trendmed\Entity\Clinic')
                        ->findForNewsletter();

                    # adding headers
                    fputcsv($fp, array_keys($output[0]));

                    if(count($output) < 1) {
                        $this->_helper->FlashMessenger(array('error' => 'W wybranej grupie nie ma adresów email'));
                        $this->_helper->Redirector('export-emails-to-csv');
                    }

                    foreach($output as $row) {
                        fputcsv($fp, $row, ';');
                    }
                    break;
                case 'patients':
                    $output = $this->_em->getRepository('\Trendmed\Entity\Patient')
                        ->findForNewsletter();

                    # adding headers
                    if(count($output) < 1) {
                        $this->_helper->FlashMessenger(array('error' => 'W wybranej grupie nie ma adresów email'));
                        $this->_helper->Redirector('export-emails-to-csv');
                    }

                    fputcsv($fp, array_keys($output[0]));

                    foreach($output as $row) {
                        fputcsv($fp, $row, ';');
                    }
                    break;
                default:
                    throw new \Exception('Not defined CSV output type given');
                    break;
            }
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $this->getResponse()
                ->setHeader('Content-Type', 'text/csv')
                ->setHeader('Content-Encoding', 'utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename=export.csv')
                ->setBody(file_get_contents(APPLICATION_PATH . '/../public/csvTemp/export.csv'));
            fclose($fp);


        }
    }
}
?>
