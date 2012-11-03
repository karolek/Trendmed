<?php
abstract class Me_User_Form_Login extends Twitter_Form
{
    /**
     * Adds a form element named "referrer" and sets its default value to either
     * the 'referrer' param from the request, or the HTTP_REFERER header.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Form
     * @author Corey Frang
     */
    public function trackReferrer(Zend_Controller_Request_Abstract $request)
    {
        $this->_addRefererElement();
        $this->setDefault('referer',
            $request->getParam('referer',
                $request->getServer('HTTP_REFERER')));

        return $this;
    }

    /**
     * Returns the referrer field if it exists.
     *
     * @return string | false
     * @param mixed $default The value to return if referrer isn't set
     * @author Corey Frang
     **/
    public function getReferer($default = null)
    {
        if (!($this->getElement('referer'))) return $default;
        $val = $this->getElement('referer')->getValue();
        if ($val) return $val;
        return $default;
    }

    /**
     * Manualy set referer.
     * Used in login call to action modal box
     *
     * @param $refererUrl
     * @return Me_User_Form_Login
     */
    public function setReferer($refererUrl)
    {
        $this->_addRefererElement();
        $this->setDefault('referer', $refererUrl);

        return $this;
    }

    protected function _addRefererElement()
    {
        $this->addElement('hidden', 'referer');
        return $this->getElement('referer');
    }
}