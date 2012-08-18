<?php
/**
 * Reservation form
 */
class Catalog_Form_Reservation extends \Twitter_Form
{
    public function init()
    {
        $this->setName("reservation");
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');
        $services = new \Zend_Form_Element_MultiCheckbox('services');
        $services->setLabel('Select services you are interest in');
        $services->setRegisterInArrayValidator(false);

        $this->addElement($services);


        $today = new \DateTime();
        $from = new \Zend_Form_Element_Text('dateFrom');
        $from->setAttrib('class', 'datepicker');
        $from->setLabel('From');
        $from->setRequired(true);
        $from->setAttrib('data-date-format', 'dd-mm-yyyy');
        $from->setValue($today->format("d-m-Y"));
        $this->addElement($from);

        $to = new \Zend_Form_Element_Text('dateTo');
        $to->setAttrib('class', 'datepicker');
        $to->setLabel('To');
        $to->setRequired(true);
        $to->setAttrib('data-date-format', 'dd-mm-yyyy');
        $to->setValue($today->add(new \DateInterval('P1W'))->format("d-m-Y"));
        $this->addElement($to);

        $question = new \Zend_Form_Element_Textarea('question');
        $question->setLabel('Additional questions or requirments to clinic');
        $this->addElement($question);

        $submit      = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Upload');

        $this->addElement($submit);
    }

    public function populateServicesFromClinic(\Trendmed\Entity\Clinic $clinic)
    {
        if(0 == $clinic->services->count()) return null;

        foreach($clinic->services as $service)
        {
            $this->getElement('services')->addMultiOption($service->id, $service->category->name. '(' .$service->priceMin.' Euro - '.$service->priceMax.' Euro)');
        }
    }
}