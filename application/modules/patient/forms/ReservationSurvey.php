<?php

/**
 * Form used for rateing reservation by patients
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Patient_Form_ReservationSurvey extends Twitter_Form
{
    public function init()
    {
        $config = Zend_Registry::get('config');
        $this->setMethod('post');
        $this->setName('reservation_survey');

        $priceRate = new \Zend_Form_Element_Radio('priceRate');
        for ($i = 1; $i <= 10; $i++) {
            $priceRate->addMultiOption($i, $i);
        }
        $priceRate->setLabel('How do you rate the price of the service from 1 to 10');
        $priceRate->setDescription('10 is the best, 1 is the worst');
        $this->addElement($priceRate);

        $serviceRate = new \Zend_Form_Element_Radio('serviceRate');
        for ($i = 1; $i <= 10; $i++) {
            $serviceRate->addMultiOption($i, $i);
        }
        $serviceRate->setLabel('How do you rate the service itself from 1 to 10');
        $serviceRate->setDescription('10 is the best, 1 is the worst');

        $this->addElement($serviceRate);

        $stuffRate = new \Zend_Form_Element_Radio('stuffRate');
        for ($i = 1; $i <= 10; $i++) {
            $stuffRate->addMultiOption($i, $i);
        }

        $stuffRate->setLabel('How do you rate the stuff of the clinic from 1 to 10');
        $stuffRate->setDescription('10 is the best, 1 is the worst');
        $stuffRate->setRequired(true);

        $this->addElement($stuffRate);


        # textarea for clinic anwser to reservation
        $comment = new \Zend_Form_Element_Textarea('comment');
        $comment->setLabel('Any aditional comments about what you think about your visist you would like to share');
        $comment->addFilter('StripTags');
        $this->addElement($comment);

        #submit button
        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');
        $submit->setAttrib('class', 'confirm');
        $this->addElement($submit);

    }
}