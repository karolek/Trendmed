<?php
interface Me_User_Model_User_Interface {
    public function getEmailaddress();
    public function getUsername();
    public function getRole();
    public function authorize($password, $rememberMe);
    public function sendWelcomeEmail();
    public function generatePasswordRecoveryToken();
    public function sendPasswordRecoveryToken();
    public function tokenIsValid($token);
    public function getMapper();

}