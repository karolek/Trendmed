<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Br_View_Helper_UserMenu extends Zend_View_Helper_Abstract
{
	public $view;
	
	public function setView(Zend_View_Interface $view)
	{
	   $this->view = $view;
	}
	
	public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }
	
	/**
	 * Checks what kind of user is logged and returns user menu for it role
	 *
	 */
	public function UserMenu()
	{
	   // we first get the user
	   $user = $this->view->LoggedUser();
	   if(!$user) return;
	   $roleName = $user->getRole()->getName();
	   $scriptName = '_' . strtolower($roleName) . 'Menu';
	   $output = $this->view->render($scriptName);
	   return $output;
	   
	}
}
?>