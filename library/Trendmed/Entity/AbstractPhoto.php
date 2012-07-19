<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use \Me\Common\Dir as Dir;
/**
 * @ORM\MappedSuperClass
 */
abstract class AbstractPhoto extends \Me\Model\ModelAbstract
{
    public function __construct(array $options = null)
    {
        $this->created = new \DateTime;
        $this->isActive = false;
        $this->isCropped = false;
        $this->viewCount = 0;
        parent::__construct($options);
    }

    /**
     * @var int value to use in processFile for compression
     */
    protected $_processFileCompression = 85;

    /** PROPERTIES **/

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var type
     */
    protected $description;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\SortablePosition
     * @var type
     */
    protected $sequence;

    /**
     * @ORM\Column(type="boolean")
     * @var type
     */
    protected $isActive;

    /**
     * @ORM\Column(type="boolean")
     * @var type
     */
    protected $isCropped;

    /**
     * @ORM\Column(type="integer")
     * @var type
     */
    protected $viewCount;

    /**
     * @ORM\Column(type="string")
     * @var string Directory in with the photos are stores (filename is a misspealing)
     */
    protected $filename;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $originalWidth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $originalHeight;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string Name of the original file
     */
    protected $originalFileName;

    /**
     * Name of this photo instance, e.g. "EntryPhoto" or "ProjectPhoto".
     * This will be used to fetch data for configuration so please make sure it's correct
     * Otherwise some things like processPhoto() may not work
     */
    protected $_photoTypeName = null;

    /**
     * @var \Zend_Config Zend config object with holds all specified configrations for this photo entity
     */
    protected $_photoConfig = null;

    /** END PROPERTIES **/

    /** GETTERS AND SETTERS **/

    public function getId() {
        return (int) $this->id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getSequence() {
        return (int) $this->sequence;
    }

    public function setSequence($sequence) {
        $this->sequence = (int) $sequence;
    }

    public function getIsActive() {
        return (bool) $this->isActive;
    }

    /**
     * Alias function for getIsActive
     * @return bool|type
     */
    public function isActive() {
        return $this->getIsActive();
    }

    public function setIsActive($isActive) {
        $this->isActive = (bool) $isActive;
    }

    public function getViewCount() {
        return (int) $this->viewCount;
    }

    public function setViewCount($viewCount) {
        $this->viewCount = (int) $viewCount;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated(\DateTime $created) {
        $this->created = $created;
    }

    public function getModified() {
        return $this->modified;
    }

    public function setModified(\DateTime $modified) {
        $this->modified = $modified;
    }


    public function setPhotoConfig(\Zend_Config $config)
    {
        $this->_photoConfig = $config;
        return $this;
    }

    public function getPhotoConfig()
    {
        if(!$this->_photoConfig) {
            // fetching config for this photo entity
            $config = \Zend_Registry::get('config');
            $configName = $this->_photoTypeName;
            if(!$configName) {
                throw new \Exception('_photoTypeName variable not defined for '.get_class($this));
            }
            if(!$photoConfig = $config->$configName->photo) throw new \Exception(
                'No config in application.ini for photo '.$configName.', please define it. E.g.
                photoTypeName.photo.uploadDir = x'
            );
            // checking if config is valid
            if(count($photoConfig->sizes) < 1) {
                throw new \Exception('Photo config should have atleast one "size" position');
            }
            if(!$photoConfig->uploadDir) {
                throw new \Exception(
                    'Photo config should have "uploadDir" value to know where You want to upload Your photos'
                );
            }
            if(!$photoConfig->publicDir) {
                throw new \Exception(
                    'Photo config should have "publicDir" value to know the URL of the photos'
                );
            }
            $this->setPhotoConfig($photoConfig);
        }
        return $this->_photoConfig;
    }

    public function setOriginalWidth($originalWidth)
    {
        $this->originalWidth = (int) $originalWidth;
    }

    public function getOriginalWidth()
    {
        return (int)$this->originalWidth;
    }

    public function setOriginalHeight($originalHeight)
    {
        $this->originalHeight = (int) $originalHeight;
    }

    public function getOriginalHeight()
    {
        return (int) $this->originalHeight;
    }

    /**
     * @return string Absolute path to directory that contains the photo files
     */
    public function getPathToPhotos()
    {
        $path = $this->getPhotoConfig()->uploadDir . $this->getFilename();
        return $path;
    }

    /**
     * @param bool $isCropped
     */
    public function setIsCropped($isCropped)
    {
        $this->isCropped = (bool) $isCropped;
    }

    /**
     * @return bool
     */
    public function getIsCropped()
    {
        return (bool) $this->isCropped;
    }

    /** END GETTERS AND SETTERS **/

    /** UTILITY METHODS **/



    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function validate()
    {
        if (!$this->getFilename())
            throw new \Exception(
                'Photo should have a directory name (filename) pointing to a dir with photo images'
            );
    }

    /*
     * This function process one image file to all thumbnails with given parameters
     * It takes tmp file uploaded by user in FILES array or already existing file
     * It makes thumbs based on application.ini configuration, it can also make cropped images
     *
     * @param $file string Path to a file (from temp or from existing source)
     * @cropOptions array
     *      @param int $left x1 position
     *      @param int $top  y1 position
     *      @param int $right x2 position
     *      @param int $bottom y2 position
     *      @param string $takenFromSize from what size of image the parameters are taken from
     */
    public function processUpload($file = null, $cropOptions = array())
    {
        require_once('class.upload.php');
        $log = \Zend_Registry::get('log');

        // setting up a index of $_FILES from config or default
        if(!$index = $this->getPhotoConfig()->filesIndex) {
            $index = 'photo'; //default value
        }
        if($file !== null) {
            // probably You want to reprocess allready proccess file
            // to regenerate thumbnails
            $handle = new \upload($file);
            $localFile = true; // marker
            // we want to use the same dir, to overwrite current files
            if(!$dir = $this->getFilename()) {
                $dir = $this->_generateDirectoryForPhoto();
            }
        } elseif($_FILES[$index]) { //this is a new photo
            $handle = new \upload($_FILES[$index]);
            // setting object original width and height
            $this->setOriginalHeight($handle->image_src_y);
            $this->setOriginalWidth($handle->image_src_x);
            $log->debug('_preserveOriginalFile');
            // we need to preserve somewhere an original photo
            $this->_preserveOriginalFile($_FILES[$index]);
            // we want to generate a new dir
            $dir = $this->_generateDirectoryForPhoto();
        } else {
            throw new \Exception('Neither $_FILES array is present or $file argument');
        }

        if ($handle->uploaded) {

            // original
            $handle->file_new_name_body = 'original';
            $handle->file_new_name_ext = 'jpg';
            $handle->image_convert = 'jpg';
            $handle->jpeg_quality = 100; // original file should stay uncompressed
            $handle->file_overwrite = true;
            $handle->process($this->getPhotoConfig()->uploadDir . $dir);

            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }
            // and now we iterate through all photo configurations in application.ini
            foreach ($this->getPhotoConfig()->sizes as $key  => $value) {
                $log->debug('Procesowanie rozmiaru: '.$key);
                $handle->file_new_name_body = $key;

                // checking if we should crop the image
                if($value->crop == true AND !empty($cropOptions)) {
                    $log->debug('cropowanie... '.$key);
                    $calculations = $this->_calculateCrop(
                        $cropOptions['left'],
                        $cropOptions['top'],
                        $cropOptions['right'],
                        $cropOptions['bottom'],
                        $cropOptions['takenFromSize']
                    );
                    $handle->image_precrop = array(
                        $calculations['topPercent'] .'%',
                        $calculations['rightPercent'].'%',
                        $calculations['bottomPercent'].'%',
                        $calculations['leftPercent'].'%'
                    );
                } else {
                    $log->debug('nie bedzie cropowanie dla '.$key);
                }
                // checking if config has width value defined
                if($value->width > 0) {
                    $handle->image_resize       = true;
                    $handle->image_x            = $value->width;
                    $handle->image_ratio_y      = true;
                    $log->debug('skalowanie do szerokosci '.$value->width);
                }
                $handle->jpeg_quality       = $this->_processFileCompression; // you can overwrite it in You subclass
                $handle->file_new_name_ext  = 'jpg';
                $handle->image_convert      = 'jpg';
                $handle->file_overwrite     = true;
                $log->debug(
                    'process dla rozmiaru '. $key .' do katalogu '.$this->getPhotoConfig()->uploadDir . $dir
                );
                $handle->process($this->getPhotoConfig()->uploadDir . $dir);
                if ($handle->processed) {
                    //$handle->clean();  // clean will remove the original file
                } else {
                    throw new \Exception('Upload errors: '.$handle->error);
                }
            }
            if(!$localFile) {
                $handle->clean(); // delete temp if it's temp file
            }
            $filename = $dir;
            $this->setFilename($filename);
            return true;
        } else {
            throw new \Exception('Handle is not uploaded');
        }
    }

    protected function _calculateCrop($left, $top, $right, $bottom, $takenFromSize = 'original')
    {
        require_once('class.upload.php');

        // file to crop, the original one
        $file = $this->getPathToPhotos().'/'.$takenFromSize . '.jpg';
        if(!file_exists($file)) {
            throw new \Exception($file. ' does not exist, cannot crop');
        }
        $handle = new \upload($file);
        // we need to do some calculations of the parameters first
        // we always must crop by % values no px, becose user could have been selecting on smaller image than original
        // we need to calculate percent values
        $width      = $handle->image_src_x;
        $height     = $handle->image_src_y;

        // first we calculate how much percent user want to crop on left side
        $calculations = array();
        if($left > 0) {
            $calculations['leftPercent'] = ($left / $width) * 100;
        } else {
            $calculations['leftPercent'] = 0;
        }
        // next we calculate top percent
        if($top > 0) {
            $calculations['topPercent'] = ($top / $height) * 100;
        } else {
            $calculations['topPercent'] = 0;
        }
        // right side
        if($right) {
            $calculations['rightPercent'] = (($width - $right) / $width) * 100;
        } else {
            $calculations['rightPercent'] = 0;
        }
        // bottom
        if ($bottom) {
            $calculations['bottomPercent'] = (($height - $bottom) / $height) * 100;
        } else {
            $calculations['bottomPercent'] = 0;
        }
        return $calculations;
    }

    /**
     * Generates directory name for a photo based on project and it's user artibutes
     * @return string
     */
    protected function _generateDirectoryForPhoto()
    {
        $dir = substr(md5(time() + rand(1, 9999999)), rand(0, 19), 14);
        return $dir;
    }

    /**
     * @ORM\PreRemove
     * Deletes nicely a photo in project
     * @throws \Exception on directory not exists
     */
    public function deleteFile()
    {
        // make sure if its ok to delete
        // delete all files in the photo folder
        $dir = $this->getFilename();
        if(!$dir) {
            throw new \Exception('No directory for files of photo object');
        }
        $dir = $this->_photoConfig->uploadDir . $dir;
        Dir::rrmdir($dir);
        // call the mapper and delete the object
    }

    /**
     * Return path to original photofile
     */
    public function getOriginalFile()
    {

    }

    protected function _preserveOriginalFile($filesArray)
    {
        $log = \Zend_Registry::get('log');
        $config = $this->getPhotoConfig();
        $uploadsDirectory = $config->uploadDir . 'originals/' . $this->_generateDirectoryForPhoto();
        $log->debug('move uploaded file to ' . $uploadsDirectory . '/'.$filesArray['name']. ' from '.$filesArray['tmp_name']);
        mkdir($uploadsDirectory);
        if(false === file_exists($uploadsDirectory)) {
            throw new \Exception('Directory for original '.$uploadsDirectory.' does not exist');
        }
        if(false === copy($filesArray['tmp_name'], $uploadsDirectory . '/' .$filesArray['name'])) {
            throw new \Exception('cannot preserve original file');
        }
        $this->setOriginalFileName($uploadsDirectory.'/'.$filesArray['name']);
    }

    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;
    }

    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }


    /** END UTILITY METHODS **/

}
