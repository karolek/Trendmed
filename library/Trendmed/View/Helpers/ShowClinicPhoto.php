<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Trendmed_View_Helpers_ShowClinicPhoto extends Zend_View_Helper_Abstract
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
	public function ShowClinicPhoto($photo, $size = "small")
	{
        $config = Zend_Registry::get('config');
        // set up default image and alt
        $src = $config->clinics->photo->publicDir . 'default/' . $size . '.png';
        $alt = 'Photo placeholder';

        if($photo instanceof \Trendmed\Entity\ClinicPhoto) { // if photo object is given
            $filePath = $config->clinics->photo->uploadDir . $photo->getPhotoDir() . '/' . $size . '.jpg';
            $fileUrl = $config->clinics->photo->publicDir . $photo->getPhotoDir() . '/' . $size . '.jpg';

            if (file_exists($filePath)) {
                $src = $fileUrl;
                $alt = $photo->clinic->name;
            }
        }
        $output = '<img src="' . $src . '" alt="Photo: ' . $alt . '" class="photo-' . $size . '" />';
        return $output;
	}
}
?>