<?php
interface Me_User_Model_User_Mapper_Interface {
    public function findByUsername($username);
    public function findByToken($token);
}