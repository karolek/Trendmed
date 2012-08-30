<?php

/**
 * Form used in actions for reservation, like confirm or cancel reservaton
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_Form_ReservationNewDate extends Clinic_Form_ReservationAnwser
{
    public function init()
    {
        parent::init();
        $today = new \DateTime();
        $from = new \Zend_Form_Element_Text('alternativeDateFrom');
        $from->setAttrib('class', 'datepicker');
        $from->setLabel('From');
        $from->setRequired(true);
        $from->setAttrib('data-date-format', 'dd-mm-yyyy');
        $from->setValue($today->format("d-m-Y"));
        $this->addElement($from);

        $to = new \Zend_Form_Element_Text('alternativeDateTo');
        $to->setAttrib('class', 'datepicker');
        $to->setLabel('To');
        $to->setRequired(true);
        $to->setAttrib('data-date-format', 'dd-mm-yyyy');
        $to->setValue($today->add(new \DateInterval('P1W'))->format("d-m-Y"));
        $this->addElement($to);

    }
}