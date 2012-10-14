<?php

/**
 * Form used in actions for reservation, like confirm or cancel reservaton
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_Form_ReservationAnwser extends Twitter_Form
{
    public function init()
    {
        $config = Zend_Registry::get('config');
        $this->setMethod('post');
        $this->setName('reservation_anwser');

        #textarea for clinic anwser to reservation
        $anwser = new \Zend_Form_Element_Textarea('anwser');
        $anwser->setLabel('Twoja odpowiedź do rezerwującego');
        $anwser->setDescription('Jeżeli klient ma pytania lub wątpliwości, użyj tego pola aby na nie odpowiedzieć.
            Możesz też przekazać inne informacje rezerwującemu.');
        $anwser->addFilter('StripTags');
        $this->addElement($anwser);

    }

    public function addSubmitWithLabel($label)
    {
        #submit button
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel($label);
        $submit->setAttrib('class', 'confirm');
        $submit->setOrder(999);
        $this->addElement($submit);
    }
}