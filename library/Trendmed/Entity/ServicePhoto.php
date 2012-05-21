<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 *
 * @ORM\Table(name="service_photo")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ServicePhoto extends \Trendmed\Entity\PhotoSet {
    /* PROPERTIES */
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Trendmed\Entity\Service", inversedBy="photos")
     * @Gedmo\SortableGroup
     */
    protected $service;

    /**
     * @var
     * @ORM\Column(type="string")
     */
    protected $photoDir;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setService($service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        return $this->service;
    }
    /**
     * @param  $photoDir
     */
    public function setPhotoDir($photoDir)
    {
        $this->photoDir = $photoDir;
    }

    /**
     * @return
     */
    public function getPhotoDir()
    {
        return $this->photoDir;
    }



    /* END GETTERS AND SETTERS */
    /* METHODS */

    public function processFile()
    {
        require_once('class.upload.php');
        $handle = new \upload($_FILES['photo']);

        // since this is avatar we want that every new
        // avatar will overwrite the old ones

        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');
        if ($handle->uploaded) {
            $log->debug('service photo file uploaded');
            // original
            $handle->file_new_name_body = 'original';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $dir = $this->_generateDirectoryForPhoto();
            $handle->process($config->services->photo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // big
            $handle->file_new_name_body = 'big';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->services->photo->sizes->big->width;
            $handle->image_y        = $config->services->photo->sizes->big->height;
            $handle->image_ratio_crop = true;
            $handle->process($config->services->photo->uploadDir . $dir);

            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // medium
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->services->photo->sizes->medium->width;
            $handle->image_y        = $config->services->photo->sizes->medium->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'medium';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->photo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // small
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->services->photo->sizes->small->width;
            $handle->image_y        = $config->services->photo->sizes->small->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'small';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->photo->uploadDir . $dir);
            if ($handle->processed) {
                $handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }
            $filename = $dir;
        } else {
            throw new \Exception('File for service photo not in $_FILES array: '.$handle->error);
        }
        $this->setPhotoDir($filename);
        return true;
    }

    public function _generateDirectoryForPhoto()
    {
        return substr(md5($this->service->id . time() . rand(1, 999999)), 0, 12);

    }

    /**
     * @ORM\PreRemove
     * @return ClinicPhoto
     * @throws \Exception
     */
    public function onDelete()
    {
        // make sure if its ok to delete
        // delete all files in the photo folder
        $dir = $this->getPhotoDir();
        if(!$dir) {
            throw new \Exception('No directory for files of photo object');
        }
        $config = \Zend_Registry::get('config');
        $dir = $config->services->photo->uploadDir . $dir;
        \Me\Common\Dir::rrmdir($dir);
        $this->setPhotoDir(null);
        return $this;
    }

    /* END METHODS */
}