<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 *
 * @ORM\Table(name="clinic_photos")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicPhoto extends \Trendmed\Entity\PhotoSet {
    /* PROPERTIES */
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic", inversedBy="photos")
     * @Gedmo\SortableGroup
     */
    protected $clinic;

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
     * @param  $clinic
     */
    public function setClinic($clinic)
    {
        $this->clinic = $clinic;
    }

    /**
     * @return
     */
    public function getClinic()
    {
        return $this->clinic;
    }

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
            $log->debug('logo file uploaded');
            // original
            $handle->file_new_name_body = 'original';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $dir = $this->_generateDirectoryForPhoto();
            $handle->process($config->clinics->photo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new Exception('Upload errors: '.$handle->error);
            }

            // big
            $handle->file_new_name_body = 'big';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->clinics->photo->sizes->big->width;
            $handle->image_y        = $config->clinics->photo->sizes->big->height;
            $handle->image_ratio_crop = true;
            $handle->process($config->clinics->photo->uploadDir . $dir);

            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new Exception('Upload errors: '.$handle->error);
            }

            // medium
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->clinics->photo->sizes->medium->width;
            $handle->image_y        = $config->clinics->photo->sizes->medium->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'medium';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->photo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new Exception('Upload errors: '.$handle->error);
            }

            // small
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->clinics->photo->sizes->small->width;
            $handle->image_y        = $config->clinics->photo->sizes->small->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'small';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->photo->uploadDir . $dir);
            if ($handle->processed) {
                $handle->clean();
            } else {
                throw new Exception('Upload errors: '.$handle->error);
            }
            $filename = $dir;
        }
        $this->setPhotoDir($filename);
        return true;
    }

    public function _generateDirectoryForPhoto()
    {
        return substr(md5($this->clinic->id . time() . rand(1, 999999)), 0, 12);

    }


    /* END METHODS */

}