<?php
namespace Me\User\Model;
/**
* 
*/
abstract class User extends Me\Model
implements Me_User_Model_User_Interface
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_salt;
    protected $_role;
    protected $_token;
    protected $_tokenValidUntil;
    protected $_lastLoginTime;
    
    public function getLastLoginTime() {
        return $this->_lastLoginTime;
    }

    public function setLastLoginTime($lastLoginTime) {
        $this->_lastLoginTime = (int) $lastLoginTime;
        return $this;
    }
        
    public function getToken()
    {
        return $this->_token;
    }
    
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }
    
    public function getTokenValidUntil()
    {
        return $this->_tokenValidUntil;
    }
    
    public function setTokenValidUntil($timestamp)
    {
        $this->_tokenValidUntil = $timestamp;
        return $this;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setPassword($password)
    {
        $this->_password = $this->_credentialGenerate($password); // has to be same method as in authorize method
        return $this;
    }
    
    public function getSalt()
    {
      return $this->_salt;
    }

    public function setSalt($salt)
    {
      $this->_salt = $salt;
      return $this;
    }
    
    public function getRole()
    {
      return $this->_role;
    }

    public function setRole(Acl_Model_Role $role)
    {
      $this->_role = $role;
      return $this;
    }
    
    public function getEmailaddress()
    {
        return $this->getUsername();
    }
    
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
	    $adapter = $this->_getAuthAdapter();
	    $adapter->setIdentity($this->getUsername())->setCredential($password);
	    $auth = Zend_Auth::getInstance();
	    $result = $auth->authenticate($adapter);
	    if($result->isValid()) {
	        if($rememberMe > 0) {
	            // remember the user for amount of time
	            Zend_Session::rememberMe(60 * 60 * $rememberMe); 
	        } else {
	            // do not remember the session after browser termination
	            Zend_Session::forgetMe();
	        }
            $this->setLastLoginTime(time());
	        $auth->getStorage()->write($this); // saveing userModel to session to use by
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
        $mail = new Zend_Mail();
        $config = Zend_Registry::get('config');
        $log = Zend_Registry::get('log');
        $mail->setBodyText('This is the text of the welcome e-mail.');
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getEmailaddress(), $this->getUsername());
        $mail->setSubject($config->siteEmail->welcomeEmailSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . ' 
        from '.$mail->getFrom() . ' subject: ' . $mail->getSubject());
    }
    
    public function generatePasswordRecoveryToken()
    {
        $string = md5($this->getEmailaddress() . $this->getId() . time());
        $token = substr($string, 12);
        $this->setToken($token);
        $config = Zend_Registry::get('config');
        // setting the token valid time, it can be changed in config/application.ini;
        
        $h = $config->usersAccounts->tokenValidTimeInHours;
        $time = strtotime("+ $h hours");
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
        $mail = new Zend_Mail();
        $config = Zend_Registry::get('config');
        $log = Zend_Registry::get('log');
        // we'r setting the password recovery link
        // we should check if the token is valid, if not, we should generate new one
        $token = $this->getToken();
        if(!$this->tokenIsValid($token)) {
            $token = $this->generatePasswordRecoveryToken()->getToken();
        }
        $link = sprintf($link, $token);

        $mail->setBodyText('This is Your password recovery link: '.$link);
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
        $mail->addTo($this->getEmailaddress(), $this->getUsername());
        $mail->setSubject($config->siteEmail->passwordRecoveryEmailSubject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . ' 
        from '.$mail->getFrom() . ' subject: ' . $mail->getSubject());    }
    
    /**
     * Checks if token if given token is valid for this user
     *
     * @param   string    $token  The token we want to validate
     * @return  bool    True if valid, false if not
     */
    public function tokenIsValid($token)
    {
        // we first check if given token is the same as one in model
        if($token != $this->getToken()) {
            return false;
        }
        // then we check if it's still valid
        if($this->getTokenValidUntil() < time()) {
            return false;
        }
        return true;
    }
    
    protected function _credentialGenerate($password)
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
	
	
}
