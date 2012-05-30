<?php

/**
 * Description of Service
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic_Form_Settings extends Twitter_Form
{
    public function init()
    {
        $this->setName('clinic_settings');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-horizontal');

        $config = \Zend_Registry::get('config');

        // want bill setting
        $wantBill = new \Zend_Form_Element_Checkbox('wantBill');
        $wantBill->setLabel('Czy wymagasz wpłaty kaucji?');
        $wantBill->setDescription('Możesz wymagać wpłaty kaucji od pacjentów przed potwierdzeniem rezerwacji.
        Kwota kaucji jest stała i wynosi '.$config->clinics->settings->billSize.' EURO');

        $this->addElement($wantBill);

        // bank account no
        $bankAccount = new \Zend_Form_Element_Text('bankAccount');
        $bankAccount->setLabel('Numer konta placówki');
        $bankAccount->setDescription(
            'Na ten numer konta będą wpływać kaucje od użytkowników, w przypadku opłaty kaucji'
        );
        $this->addElement($bankAccount);

        // submit
        $submit = new \Zend_Form_Element_Submit('save');
        $submit->setLabel('Zapisz ustawienia');
        $this->addElement($submit);
    }
}