<?php
$I = new WebGuy($scenario);
$I->wantTo('register new user');
$I->amOnPage('/user/register');
$I->fillField('username','Mateusz88@o2.pl');
$I->fillField('password','TheSandals');
$I->fillField('password_confirmation','TheSandals');
$I->click('Sign In!');
$I->see('You have registered succesfuly. You can login now.');

?>