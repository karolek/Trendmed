<?php
$I = new WebGuy($scenario);
$I->wantTo('register new user');
$I->amOnPage('/user/register');
$I->fillField('username','Mateusz88@o2.pl');
$I->fillField('password','TheSandals');
$I->fillField('password_confirmation','TheSandals');
$I->click('Sign In!');
$I->see('You have registered succesfuly. You can login now.');
$I->seeInDatabase('acluser', array('email' => 'Mateusz88@o2.pl'));

$I = new WebGuy($scenario);
$I->wantTo('generate link to set up new password');
$I->amOnPage('/user');
$I->click('Password recovery');
$I->seeInUrl('/user/index/password-recovery');
$I->amOnPage('/user/index/password-recovery');
$I->fillField('username', 'Mateusz88@o2.pl');
$I->click('Recover');
$I->see('Recovery password e-mail send to: Mateusz88@o2.pl');
?>