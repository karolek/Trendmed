<?php
class Me_Validate_ServicePrice extends \Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Price max must be greater that price min'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context['pricemin'])
                && ($value > $context['pricemin']))
            {
                return true;
            }
        } elseif (is_string($context) && ($value > $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}