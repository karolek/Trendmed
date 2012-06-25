<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Trendmed_View_Helper_ShowServicePhoto extends Zend_View_Helper_Abstract
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
	

	public function ShowServicePhoto($photo, $size = "small")
	{
        $config = Zend_Registry::get('config');
        // set up default image and alt
        $src = $config->services->photo->publicDir . 'default/' . $size . '.png';
        $alt = 'Photo placeholder';

        if($photo instanceof \Trendmed\Entity\ServicePhoto) { // if photo object is given
            $filePath = $config->services->photo->uploadDir . $photo->getPhotoDir() . '/' . $size . '.jpg';
            $fileUrl = $config->services->photo->publicDir . $photo->getPhotoDir() . '/' . $size . '.jpg';

            if (file_exists($filePath)) {
                $src = $fileUrl;
                $alt = $photo->service->category->name;
            }
        }
        $output = '<img src="' . $src . '" alt="Photo: ' . $alt . '" class="photo-' . $size . '" />';
        return $output;
	}
}
?>