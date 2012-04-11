<?php
$I = new WebGuy($scenario);
$I->wantTo('check the system will not find me in db');
$I->amOnPage('/user');
$I->fillField('username','I do not exist');
$I->fillField('password','I does not matter, I do not exists');
$I->click('Sign In!');
$I->see('No such user in database as');

$I = new WebGuy($scenario);
$I->wantTo('check if the user if found is properly authorized');
$I->amOnPage('/user');
$I->fillField('username','bartosz.rychlicki@gmail.com');
$I->fillField('password','I does not matter, I do not want to login');
$I->click('Sign In!');
$I->see('Wrong login or password supplied');

$I = new WebGuy($scenario);
$I->wantTo('Empty login form will return an validation error');
$I->amOnPage('/user');
$I->fillField('username','');
$I->fillField('password','I does not matter, I do not want to login');
$I->click('Sign In!');
$I->see("Value is required and can't be empty");

$I = new WebGuy($scenario);
$I->wantTo('Empty login form will return an validation error #2');
$I->amOnPage('/user');
$I->fillField('username','bartosz.rychlicki@gmail.com');
$I->fillField('password','');
$I->click('Sign In!');
$I->see("Value is required and can't be empty");

?>