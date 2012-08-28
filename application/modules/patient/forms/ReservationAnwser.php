<?php

/**
 * Form used in actions for reservation, like confirm or cancel reservaton
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Patient_Form_ReservationAnwser extends Twitter_Form
{
    public function init()
    {
        $config = Zend_Registry::get('config');
        $this->setMethod('post');
        $this->setName('reservation_anwser');

        #textarea for clinic anwser to reservation
        $question = new \Zend_Form_Element_Textarea('question');
        $question->setLabel('Comments to clinic');
        $question->addFilter('StripTags');
        $this->addElement($question);

        #submit button
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Confirm');
        $submit->setAttrib('class', 'confirm');
        $this->addElement($submit);

    }
}