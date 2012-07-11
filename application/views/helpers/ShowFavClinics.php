<?php
class Trendmed_View_Helper_ShowFavClinics extends Zend_View_Helper_Abstract
{
    public $view;
    protected $_rootNode;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function ShowFavClinics(\Trendmed\Entity\Patient $patient, $amount = 3, $manage = true)
    {
        // first let's check if user has any fav clinics
        if($patient->favoriteClinics->count() < 1) {
            return;
        }
        // setting up view scripPath for main layouts and partial directory
        $this->view->setScriptPath(APPLICATION_PATH . '/layouts/scripts');
        $i = 0;
        $output = "";
        
        foreach ($patient->favoriteClinics as $clinic) {
            if($i > $amount) return $output;
            $output .= $this->view->partial('_favoriteClinic.phtml', array('clinic' => $clinic));
            $i++;
        }
        // also wee need to add javascript with will handle disapear of the favorite element after ajaxSuccess
        $this->view->headScript()->appendScript('
            $(function() {
                $(".add-to-fav").ajaxSuccess(function() {
                    $(this).parent(".clinic-favorite").fadeOut();
                })
            })
        ');
        return $output; 
    }
}