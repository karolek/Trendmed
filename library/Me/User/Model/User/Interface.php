<?php
interface Me_User_Model_User_Interface {
    public function getEmailaddress();
    public function getUsername();
    public function getRole();
    public function authorize($password, $rememberMe);
    public function sendWelcomeEmail();
    public function generatePasswordRecoveryToken();
    public function sendPasswordRecoveryToken($link);
    public function tokenIsValid($token);
    public function getMapper();
    public function setLastLoginTime($timestamp);
    public function getLastLoginTime();

}