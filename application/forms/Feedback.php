<?php
class Application_Form_Feedback extends Twitter_Form
{

    protected $_subject = 'Użytkownik zgłosił uwagi nt. działania Trendmed.eu';
    protected $_messageIntro = 'Z chęcią wysłuchamy jakichkolwiek uwag odnośnie funkcjonowania naszego portalu.';

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
        $message->setDescription('Wszelkie uwagi i sugestie odnośnie funkcjonalnowania naszego portalu');
        $message->setLabel('Uwagi');
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
