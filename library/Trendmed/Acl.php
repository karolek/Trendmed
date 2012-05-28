<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 28.05.12
 * Time: 21:01
 * To change this template use File | Settings | File Templates.
 */
class Trendmed_Acl extends Zend_Acl
{
    const ROLE_GUEST        = 'guest';
    const ROLE_USER         = 'user';
    const ROLE_PUBLISHER    = 'publisher';
    const ROLE_EDITOR       = 'editor';
    const ROLE_ADMIN        = 'admin';
    const ROLE_GOD          = 'god';

    protected static $_instance;

    /* Singleton pattern */
    protected function __construct()
    {
        $this->addRole(new Zend_Acl_Role(self::ROLE_GUEST));
        $this->addRole(new Zend_Acl_Role(self::ROLE_USER), self::ROLE_GUEST);
        $this->addRole(new Zend_Acl_Role(self::ROLE_PUBLISHER), self::ROLE_USER);
        $this->addRole(new Zend_Acl_Role(self::ROLE_EDITOR), self::ROLE_PUBLISHER);
        $this->addRole(new Zend_Acl_Role(self::ROLE_ADMIN), self::ROLE_EDITOR);

        //unique role for superadmin
        $this->addRole(new Zend_Acl_Role(self::ROLE_GOD));

        $this->allow(self::ROLE_GOD);

        // defining all the resources
        $this->addResource(new Zend_Acl_Resource('mvc:admin'));
        $this->addResource(new Zend_Acl_Resource('mvc:admin:login'), 'mvc:admin');
        $this->addResource(new Zend_Acl_Resource('mvc:clinic'));
        $this->addResource(new Zend_Acl_Resource('mvc:patient'));

        $this->deny('guest', 'admin')
            ->allow('guest', 'admin:login');

        return $this;
    }

    protected static $_user;

    public static function setUser($user = null)
    {
        if (null === $user) {
            throw new InvalidArgumentException('$user is null');
        }

        self::$_user = $user;
    }

    /**
     *
     * @return App_Model_Acl
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function resetInstance()
    {
        self::$_instance = null;
        self::getInstance();
    }
}



class Smapp extends Bootstrap // class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var App_Model_User
     */
    protected static $_currentUser;

    public function __construct($application)
    {
        parent::__construct($application);
    }

    public static function setCurrentUser(Users_Model_User $user)
    {
        self::$_currentUser = $user;
    }

    /**
     * @return App_Model_User
     */
    public static function getCurrentUser()
    {
        if (null === self::$_currentUser) {
            self::setCurrentUser(Users_Service_User::getUserModel());
        }
        return self::$_currentUser;
    }

    /**
     * @return App_Model_User
     */
    public static function getCurrentUserId()
    {
        $user = self::getCurrentUser();
        return $user->getId();
    }

}