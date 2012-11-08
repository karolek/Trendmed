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
     * @param $relGroup will be added in rel attribute to group photos like so rel="lightbox[roadtrip]"
     *
     */
    public function ShowPhoto($photo, $size = "small", $type = 'clinics', $relGroup = false, $disableLink = false)
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
        $relGroup ? $rel = "lightbox[$relGroup]" : $rel = 'lightbox';

        $output = '<img src="' . $src . '" alt="Photo: ' . $alt . '" class="photo-' . $size . '" />';
        if ($disableLink == false) {
            $output = '<a href="' . $bigFileUrl . '" rel="'.$rel.'">'.$output.'</a>';
        }
        return $output;
    }
}
?>