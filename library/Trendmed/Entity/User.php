<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Genric Abstract User Entity. Providing base fields and operations to use in
 * extending entities.
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class User extends \Me\Model\ModelAbstract implements \Me_User_Model_User_Interface
{
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->isActive = false;    
        $this->salt = $this->_generateSalt();
        return parent::__construct();
    }

    /* PROPERTIES */

    /**
     * @ORM\Column(type="string")
     * @var string user login
     */
    protected $login;

    /**
     * @ORM\Column(type="string")
     * @var string user
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $salt;

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Role")
     */
    protected $role;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $tokenValidUntil;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLoginTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tempEmailAddress;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var bool
     */
    protected $isActive;

    protected $_welcomeEmailScript = null; //implement in subclass, this is file of view with HTML content of welcome email
    protected $_moduleName = null; // name of the MCV module used to handle current user entity, used for links in email generation
    /* END PROPERTIES */

    /* GETTERS AND SETTERS */

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $this->_credentialGenerate($password); // has to be same method as in authorize method
        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
        $role->addToUser($this);
        return $this;
    }

    public function getToken()
    {
        return (string)$this->token;
    }

    public function setToken($token)
    {
        $this->token = (string)$token;
        return $this;
    }

    public function getTokenValidUntil()
    {
        return $this->tokenValidUntil;
    }

    public function setTokenValidUntil($tokenValidUntil)
    {
        $this->tokenValidUntil = $tokenValidUntil;
        return $this;
    }

    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    public function setLastLoginTime($lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }


    /* END GETTERS AND SETTERS */

    /**
     * Function provides authorization of a model in the sysatem.
     *
     * We check if param password == model password, if true than user is authorized.
     * Second param is for storeing the user in system for a period of time.
     *
     * @param string    $password Password to check if it's right for this model (supplied by user)
     * @param int       $rememberMe Int or bool, if int then it says for long in hours remeber the user in the system
     */
    public function authorize($password, $rememberMe = false)
    {
        $password = $this->_credentialGenerate($password); // we'r marking all the magic crypting here

        $auth = \Zend_Auth::getInstance();

        if ($password == $this->getPassword()) {
            $result = true; // yeah, the password is righr
        } else {
            $result = false; // the password for this model is wrong! beatch!
        }
        if ($result) {
            if ($rememberMe > 0) {
                // remember the user for amount of time
                \Zend_Session::rememberMe(60 * 60 * $rememberMe);
            } else {
                // do not remember the session after browser termination
                \Zend_Session::forgetMe();
            }
            $arrayToStore = array(
                'id' => $this->getId(),
                'roleName' => $this->getRole()->name,
                'entityNamespace' => get_class(),
            );
            $this->setLastLoginTime(new \DateTime());
            $auth->getStorage()->write($arrayToStore); // saveing user.id to session to use by
            // Zend_Auth
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function sends a welcome e-mail to model e-mail address.
     */
    public function sendWelcomeEmail()
    {
        $mail = new \Zend_Mail('UTF-8');
        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');

        // checking if template file is defined
        if (empty($this->_welcomeEmailScript)) {
            throw new \Exception('No content template (_welcomeEmailScript) defined for class ' . __CLASS__);
        }
        $view = \Zend_Registry::get('view');
        $htmlContent = $view->render($this->_welcomeEmailScript); // rendering a view template for content
        $mail->setBodyHtml($htmlContent);
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getEmailaddress(), $this->getLogin());
        $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
        $mail->setSubject($config->siteEmail->welcomeEmailSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . ' 
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
    }

    public function generatePasswordRecoveryToken()
    {
        if (!$this->getEmailaddress()) {
            throw new \Exception('Model should have an e-mail address to generate token');
        }
        $string = md5($this->getEmailaddress() . time());
        $token = substr($string, 12);
        // also, we need ensure that no 0 is in string, this couse some errors with gmail (it cuts them from links)
        $token = str_replace('0', 'z', $token);
        $this->setToken($token);
        $config = \Zend_Registry::get('config');
        // setting the token valid time, it can be changed in config/application.ini;

        $h = $config->usersAccounts->tokenValidTimeInHours;
        $time = new \DateTime();
        $time->add(new \DateInterval("PT" . $h . "H"));
        $this->setTokenValidUntil($time);
        return $this;
    }

    /**
     * Sends password recovery link to user.
     *
     * @param string $linkPattern Whole URL string to password recovery action.
     * Use %s instead of actual token.
     */
    public function sendPasswordRecoveryToken($link)
    {
        $mail = new \Zend_Mail('UTF-8');
        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');
        // we'r setting the password recovery link
        // we should check if the token is valid, if not, we should generate new one
        $token = $this->getToken();
        if (!$this->tokenIsValid($token)) {
            $token = $this->generatePasswordRecoveryToken()->getToken();
        }
        $link = sprintf($link, $token);

        $mail->setBodyText('This is Your password recovery link: ' . $link);
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getEmailaddress(), $this->getUsername());
        $mail->setSubject($config->siteEmail->passwordRecoveryEmailSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . ' 
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
    }

    /**
     * Checks if token if given token is valid for this user
     *
     * @param   string    $token  The token we want to validate
     * @return  bool    True if valid, false if not
     */
    public function tokenIsValid($token)
    {
        if (empty($token)) {
            return false;
        }
        // we first check if given token is the same as one in model
        if ($token != $this->getToken()) {
            return false;
        }
        // then we check if it's still valid
        if ($this->getTokenValidUntil()->getTimestamp() < time()) {
            return false;
        }
        return true;
    }

    protected static function _credentialGenerate($password)
    {
        $output = md5($password);
        return $output;
    }

    protected function _getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $table = $this->getMapper()->getDbTable();

        $authAdapter->setTableName($table->getName())
            ->setIdentityColumn($table->getIdentityColumn())
            ->setCredentialColumn($table->getCredentialColumn());

        return $authAdapter;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onSave()
    {
        $this->modified = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function validate()
    {
        if (!$this->role) {
            throw new \Exception('User must have a role before save');
        }
        if (!($this->role instanceof \Trendmed\Entity\Role)) {
            throw new \Exception('User role must be an instance of object Role, and You give:'
                . var_dump($this->role));
        }

        if (!$this->getLogin()) {
            throw new \Exception('User must have a login');
        }
        if (!$this->password) {
            throw new \Exception('User must have a login');
        }
    }

    /**
     * Implemenet this in Your extending class
     */
    public function getEmailaddress()
    {
        return;
    }

    protected function _generateSalt()
    {
        return substr(md5(rand(1, 999999) . time()), 0, 12);
    }

    public function getUsername()
    {
        return $this->getLogin();
    }

    public function sendNewPasswordEmail()
    {
        $mail = new \Zend_Mail('UTF-8');
        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');

        // checking if template file is defined
        if (empty($this->_newPasswordScript)) {
            throw new \Exception('No content template (_newPasswordScript) defined for class ' . __CLASS__);
        }
        $view = \Zend_Registry::get('view');
        $htmlContent = $view->render($this->_newPasswordScript); // rendering a view template for content
        $mail->setBodyHtml($htmlContent);
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getEmailaddress(), $this->getLogin());
        $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
        $mail->setSubject($config->siteEmail->newPasswordSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . '
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());

    }

    public function setTempEmailAddress($tempEmailAddress)
    {
        $this->tempEmailAddress = $tempEmailAddress;
    }

    public function getTempEmailAddress()
    {
        return $this->tempEmailAddress;
    }

    public function activateEmaillAddressFromTemp()
    {
        $this->setEmailaddress($this->getTempEmailAddress());
        $this->setTempEmailAddress(null);
        return $this;
    }

    public function sendNewEmailAddressEmail()
    {
        $mail = new \Zend_Mail('UTF-8');
        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');

        // checking if template file is defined
        if (empty($this->_newEmailScript)) {
            throw new \Exception('No content template (_newEmailScript) defined for class ' . __CLASS__);
        }
        // we'r setting the password recovery link
        // we should check if the token is valid, if not, we should generate new one
        $view = \Zend_Registry::get('view');
        $view->link = $this->_getEmailChangeLink();
        $htmlContent = $view->render($this->_newEmailScript); // rendering a view template for content

        $mail->setBodyHtml($htmlContent);

        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getTempEmailAddress(), $this->getUsername());
        $mail->setSubject($config->siteEmail->newEmailAddressSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . '
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
    }

    protected function _getEmailChangeLink()
    {
        if ($this->_moduleName) {
            $moduleName = $this->_moduleName;
        } else {
            $moduleName = get_class($this);
        }
        // generating token
        $token = $this->getToken();

        if (!$this->tokenIsValid($token)) {
            $token = (string)$this->generatePasswordRecoveryToken()->getToken();
        }

        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $moduleName . '/index/new-email-address-from-token/token/' . $token;

    }

    /**
     * This callback is a function that will be executed before user persitance at register.
     * Use this in You model to call for anything that You want to do before saveing
     */
    public function beforeRegister()
    {

    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Alias method for getIsActive()
     * @return bool
     */
    public function isActive()
    {
        return $this->getIsActive();
    }

    public function activate()
    {
        $this->setIsActive(true);
        return $this;
    }

    public function deactivate()
    {
        $this->setIsActive(false);
        return $this;
    }

}
