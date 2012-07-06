<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Trendmed_View_Helper_ShowPhoto extends Zend_View_Helper_Abstract
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
	 * @todo implement
	 */
	public function ShowClinicLogo($user, $size = "medium", $type = 'clinicPhoto', $makeLinkToProfile = false)
	{

        $config = Zend_Registry::get('config');
        // set up default image and alt
        $src = $config->clinics->logo->publicDir . 'default/' . $size . '.png';
        $alt = $user->name.' logo';

        if($user instanceof \Trendmed\Entity\Clinic) {
            $filePath = $config->clinics->logo->uploadDir . $user->getLogoDir() . '/' . $size . '.jpg';
            $fileUrl = $config->clinics->logo->publicDir . $user->getLogoDir() . '/' . $size . '.jpg';

            if (file_exists($filePath)) {
                $src = $fileUrl;
            }
        }
        $output = '<img src="' . $src . '" alt="Photo: ' . $alt . '" class="photo-' . $size . '" />';
        if (true === $makeLinkToProfile) {
            $output = '<a href="'.$this->view->url(
                array(
                    'action'        => 'profile',
                    'controller'    => 'public',
                    'module'        => 'clinic',
                    'slug'          => $user->getSlug(),
                ), null, true
            ) .'">'. $output .'</a>';
        }
        return $output;
	}
}