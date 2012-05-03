<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$I = new WebGuy($scenario);
$I->wantTo('login into my clinic account');
$I->amOnPage('/clinic');
$I->see('Login');
$I->fillField('username', 'bartosz.rychlicki@gmail.com');
$I->fillField('password', 'nataniel');
$I->click('signin');
$I->see('zalogowany');

