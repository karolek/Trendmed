<?php
/**
 * This helper makes a lead text out of any text.
 * It just nicely substract some amount of text from the beggining of the text.
 *
 * @package Br
 * @author Bartosz Rychlicki <b@br-design.pl>
 */
class Trendmed_View_Helper_ReservationActionLink extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    /**
     * @param Trendmed\Entity\Reservation $reservation
     * @param $actionMaker either string "patient" or "clinic"
     */
    public function reservationActionLink(\Trendmed\Entity\Reservation $reservation, $actionMaker)
    {
        if ($actionMaker != 'patient' and $actionMaker != 'clinic') {
            throw new \Exception('Action maker argument should either clinic or patient');
        }
        $status = $reservation->getStatusAsArray();
        $links = array();
        foreach($status[$actionMaker]['actions'] as $action) {
            $link = '<a href=""></a>';
            $links[] = $link;
        }
    }

}