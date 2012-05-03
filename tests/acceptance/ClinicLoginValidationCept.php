<?php
$I = new WebGuy($scenario);
$I->wantToTest('If login will fail, given wrong password');
$I->amOnPage('/clinic');
$I->see('Login');
$I->fillField('username', 'bartosz.rychlicki@gmail.com');
$I->fillField('password', 'dupadupa');
$I->click('signin');
$I->dontSee('zalogowany');
$I->see('Niepoprawny');
