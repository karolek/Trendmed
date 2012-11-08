<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use \Doctrine\Common\Collections;
use \Me\Common;
/**
 * BannerAd entity
 *
 * @ORM\table(name="bannerads")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\Trendmed\Repository\AdsRepository")
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class BannerAd extends  \Me\Model\ModelAbstract {

    public function __construct() {
        $this->created = new \DateTime();
        $this->viewCount = 0;
        $this->clickCount = 0;
        $this->shown = false;
        $this->isActive = false;
        $this->type = 'static';
        $this->setOpenIn('_self');
        parent::__construct();
    }

    /* PROPERTIES */

    /**
     * @var array types of banners (like static, flash, code). Key in array is label and value is the reference
     */
    public static $BANNER_TYPES = array(
        'static'   => 'Static banner',
    );

    /**
     * @var array list of avaible targets for link
     */
    public static $LINK_TARGETS = array(
        '_self'     => 'The same windows as link',
        '_blank'    => 'Open new window',
    );

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

        /**
     * @ORM\Column(type="datetime")
     * @var type 
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var type 
     */
    protected $modified;

    /**
     * @var string absolute path to the file of a banner (jpg, swf, gif etc), or code to display in some cases.
     * @ORM\Column(type="string")
     */
    protected $file;

    /**
     * @var string type of a banner, taken from defined class constants BannerAd::BANNER_TYPES. Should point to
     * a key in that array.
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var string brief description of this ad
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string dynamic zone name, this will allow to select and display banners for a given zone on a webpage
     * @ORM\Column(type="string")
     */
    protected $zone;

    /**
     * @var integer amount of views of the banner, every display incerements viewcoung
     * @ORM\Column(type="integer")
     */
    protected $viewCount;

    /**
     * @var bool determines if banner should be shown
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="integer")
     * @var integer amounts of clicks on the banner
     */
    protected $clickCount;

    /**
     * @var boolean if banner has been shown for this turn. It's  used to determine with banner shown next.
     * @ORM\Column(type="boolean")
     */
    protected $shown;

    /**
     * @var string Url of the add. Can be empty to point nowhere (not clickable)
     * @ORM\Column(type="string", nullable=true)
     */
    protected $target;

    /**
     * @var string should be called target, but it's taken allready, this is where link should open (_blank or _self)
     * @ORM\Column(type="string")
     */
    protected $openIn;

    /* END PROPERTIES */
    
    /* GETTERS AND SETTERS */

    /**
     * @param int $clickCount
     */
    public function setClickCount($clickCount)
    {
        $this->clickCount = $clickCount;
    }

    /**
     * @return int
     */
    public function getClickCount()
    {
        return $this->clickCount;
    }

    /**
     * @param \Trenedmed\Entity\type $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \Trenedmed\Entity\type
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;
    }

    public function activate($state = true)
    {
        $this->setIsActive($state);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    public function isActive()
    {
        return $this->getIsActive();
    }

    /**
     * @param \Trenedmed\Entity\type $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return \Trenedmed\Entity\type
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param boolean $shown
     */
    public function setShown($shown)
    {
        $this->shown = $shown;
    }

    /**
     * @return boolean
     */
    public function getShown()
    {
        return $this->shown;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @return int
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param string $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }


    /* END GETTERS AND SETTERS */

    /* METHODS */

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function validate()
    {
        if(!$this->zone) throw new \Exception('BannerAd must have a zone');
        if(!$this->file) throw new \Exception('BannerAd must have a file or code');
        if(!$this->type) throw new \Exception('BannerAd must have a type');
        if( !array_key_exists($this->type, self::$BANNER_TYPES) ) throw new \Exception(
            'Bad BannerAd type. Not defined. Given: ' . $this->type
        );
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->modified = new \DateTime;
    }

    public function processFile()
    {
        require_once('class.upload.php');
        $log = \Zend_Registry::get('log');
        $handle = new \upload($_FILES['file']);
        $config = \Zend_Registry::get('config');

        if ($handle->uploaded) {
            $log->debug('Banner file is uploaded');
            // original
            $handle->file_new_name_body = 'original';
            $handle->file_new_name_ext = 'jpg';
            $dir = substr(sha1(time()), 0, 12);
            $handle->process($config->ads->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // small
            $handle->image_resize   = true;
            $handle->image_x        = 180;
            $handle->image_y        = 180;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'small';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->ads->uploadDir . $dir);
            if ($handle->processed) {
                $handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }
            $this->setFile($dir);
            return $dir;
        } else {
            throw new \Exception(__FUNCTION__ . 'should have a superglobal $_FILES[file]');
        }
    }

    /**
     * Deletes banner directory
     * @ORM\PreRemove
     * @throws \Exception
     */
    public function onRemove()
    {
        // make sure if its ok to delete
        // delete all files in the photo folder
        $dir = $this->getFile();
        if(!$dir) {
            throw new \Exception('No directory for files of banner object');
        }
        $config = \Zend_Registry::get('config');
        $dir = $config->ads->uploadDir . $dir;
        \Me\Common\Dir::rrmdir($dir);
    }

    /**
     * @param string $openIn
     */
    public function setOpenIn($openIn)
    {
        // check if type exist in defined array
        if(!array_key_exists($openIn, self::$LINK_TARGETS)) {
            throw new \Exception('Target type '.$openIn . ' is not defined in entity BannerAd');
        }
        $this->openIn = $openIn;
    }

    /**
     * @return string
     */
    public function getOpenIn()
    {
        return $this->openIn;
    }

    /* END METHODS */

}