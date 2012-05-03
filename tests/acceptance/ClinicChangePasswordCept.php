<?php
$I = new WebGuy($scenario);
$I->wantTo('change my clinic password');
$I->amOnPage('/clinic');
$I->see('Login');
$I->fillField('username', 'bartosz.rychlicki@gmail.com');
$I->fillField('password', 'nataniel');
$I->click('signin');
$I->see('zalogowany');
$I->see('Zmień hasło');
$I->click('Zmień hasło');
$I->see('Nowe hasło');
$I->fillField('password', 'jakubek16');
$I->fillField('password_confirmation', 'jakubek16');
$I->click('Zapisz');
$I->see('Twoje hasło zostało zmienione');