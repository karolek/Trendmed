<?php
use IAA\Interfaces\Photo;
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
     *
     */
    public function ShowPhoto($photo, $size = "small", $type = 'clinics')
    {
        $config = Zend_Registry::get('config');
        // set up default image and alt
        if(!$config->$type->photo->publicDir) {
            throw new \Exception('There is no config "publicDir" for photo type '.$type);
        }
        $src = $config->$type->photo->publicDir . 'default/' . $size . '.png';

        $alt = 'Photo placeholder';

        if($photo instanceof \Trendmed\Interfaces\Photo) { // if photo object is given
            $filePath = $config->$type->photo->uploadDir . $photo->getFilename() . '/' . $size . '.jpg';
            $fileUrl = $config->$type->photo->publicDir . $photo->getFilename() . '/' . $size . '.jpg';
            $bigFileUrl = $config->$type->photo->publicDir . $photo->getFilename() . '/big.jpg';
            if (file_exists($filePath)) {
                $src = $fileUrl;
                $alt = $photo->getDescription();
            }
        }
        $output = '<a href="' . $bigFileUrl . '" rel="lightbox"><img src="' . $src . '" alt="Photo: ' . $alt . '" class="photo-' . $size . '" /></a>';
        return $output;
    }
}
?>