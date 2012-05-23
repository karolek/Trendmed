<?php
class Clinic_Form_ClinicProfile_MultiLangDesc extends Twitter_Form
{
    protected $_clinic;
    protected $_em;

    public function init()
    {
        if(!$this->_em) {
            $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        }

        $config = \Zend_Registry::get('config');

        $stripTagsValidator = new \Zend_Filter_StripTags();

        foreach($config->languages as $lang)
        {
            $desc = new \Zend_Form_Element_Textarea('description_'.$lang->code);
            $desc->setLabel('Opis placówki w języku: '.$lang->name);
            //$desc->addFilter($stripTagsValidator);
            $desc->setAttrib('class', 'ckeditor');
            $this->addElement($desc);

            $customPromo = new \Zend_Form_Element_Textarea('customPromos_' . $lang->code);
            $customPromo->setLabel(
                'Informacje o promocjach dla Twoich gości w języku '.$lang->name
            );
            //$customPromo->addFilter($stripTagsValidator);
            $customPromo->setAttrib('class', 'ckeditor');
            $this->addElement($customPromo);

        }


    }

    public function populateFromUser($clinic)
    {
        if (!$this->_clinic) {
            $this->_clinic = $clinic;
        }

        $repository = $this->_em->getRepository('\Trendmed\Entity\Translation');
        $trans = $repository->findTranslations($clinic);

        $config = \Zend_Registry::get('config');

        foreach($config->languages as $lang) {
            if($lang->default == 1) {
                $this->setDefaults(
                    array(
                        'description_'.$lang->code => $this->_clinic->description,
                        'customPromos_'.$lang->code => $this->_clinic->customPromos,
                    )
                );
            } else {
                if (isset($trans[$lang->code])) {
                    $this->setDefault('description_'.$lang->code, $trans[$lang->code]['description']);
                    $this->setDefault('customPromos_'.$lang->code, $trans[$lang->code]['customPromos']);
                }
            }

        }
    }

}