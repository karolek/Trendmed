<?php
class Application_Form_Newcategory extends Twitter_Form
{

    protected $_subject = 'Propozycja nowej kategorii na Trendmed.eu';
    protected $_messageIntro = 'Jeżeli nie znalazłeś w naszym katalogu stosownej kategorii możesz ją nam zasugerować.';

    public function init()
    {
        $this->setName('NewCategoryProposition');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $email = new \Zend_Form_Element_Text('email');
        $email->addValidator('EmailAddress');
        $email->setRequired();
        $email->setLabel('Your e-mail address');
        $this->addElement($email);

        $message = new \Zend_Form_Element_Textarea('categoryName');
        $message->setDescription('Podaj nazwe proponowanej kategorii i jej umiejscowienie w katalogu');
        $message->setLabel('Proponowana kategoria');
        $message->setRequired();

        $this->addElement($message);

        $submit = new \Zend_Form_Element_Submit('Wyślij');
        $this->addElement($submit);

    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function getMessageIntro()
    {
        return $this->_messageIntro;
    }


}
