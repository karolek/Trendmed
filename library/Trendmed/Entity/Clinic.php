<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Trendmed\Repository;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * This is clinic model.
 *
 * @ORM\Entity(repositoryClass="Trendmed\Repository\ClinicRepository")
 * @ORM\Table(name="clinics")
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic extends \Trendmed\Entity\User implements \Trendmed\Interfaces\Favoritable
{
    
    public function __construct() {
        $this->services = new \Doctrine\Common\Collections\ArrayCollection;
        $this->wantBill = false;
        $this->country = 'Poland';
        $this->roleName = 'clinic';
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection;
        $this->favoredByUsers = new \Doctrine\Common\Collections\ArrayCollection;

        return parent::__construct();
    }
    
    /* PROPERTIES */
    
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */
    protected $streetaddress;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $province;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $city;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $postcode;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $repPhone;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $repName;
    
    /**
     * @ORM\Column(type="string", unique=true);
     * @var type 
     */    
    protected $repEmail;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $type;
    
    /**
     * @ORM\Column(type="boolean");
     * @var type 
     */    
    protected $wantBill;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $country;
    
    /**
     * @ORM\Column(type="string", nullable=true);
     * @var type 
     */    
    protected $geoLat;
    
    /**
     * @ORM\Column(type="string", nullable=true);
     * @var type 
     */    
    protected $getLon;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true);
     */
    protected $description;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true);
     */
    protected $customPromos;

    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Service", mappedBy="clinic") 
     */
    protected $services;
    
    /**
     * @ORM\Column(type="string")
     * @var type 
     */
    protected $roleName; // only use as a internal helper

    /**
     * @Gedmo\Slug(fields={"name", "city"})
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string absolute path to logo directory (public url)
     */
    protected $logoDir;

    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\ClinicPhoto", mappedBy="clinic")
     */
    protected $photos;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bankAccount;

    /**
     * @ORM\ManyToMany(targetEntity="\Trendmed\Entity\Patient", mappedBy="favoriteClinics")
     */
    protected $favoredByUsers;

    protected $_welcomeEmailScript = 'register/_welcomeEmail.phtml';
    protected $_newPasswordScript   = 'register/_newPassword.phtml';
    protected $_newEmailScript      = 'profile/_newEmail.phtml';


    /* END PROPERTIES */
    /*  GETTERS AND SETTERS */
    public function getId() {
        return $this->id;
    }

        
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getStreetaddress() {
        return $this->streetaddress;
    }

    public function setStreetaddress($streetaddress) {
        $this->streetaddress = $streetaddress;
    }

    public function getProvince() {
        return $this->province;
    }

    public function setProvince($province) {
        $this->province = $province;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getPostcode() {
        return $this->postcode;
    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    public function getRepPhone() {
        return $this->repPhone;
    }

    public function setRepPhone($repPhone) {
        $this->repPhone = $repPhone;
    }

    public function getRepName() {
        return $this->repName;
    }

   public function setRepName($repName) {
        $this->repName = $repName;
    }

    public function getRepEmail() {
        return $this->repEmail;
    }

    public function setRepEmail($repEmail) {
        $this->repEmail = $repEmail;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getWantBill() {
        return $this->wantBill;
    }

    public function setWantBill($wantBill) {
        $this->wantBill = $wantBill;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function getGeoLat() {
        return $this->geoLat;
    }

    public function setGeoLat($geoLat) {
        $this->geoLat = $geoLat;
    }

    public function getGetLon() {
        return $this->getLon;
    }

    public function setGetLon($getLon) {
        $this->getLon = $getLon;
    }

    public function getServices() {
        return $this->services;
    }

    public function setServices($services) {
        $this->services = $services;
    }
    
    public function getRoleName() {
        return $this->roleName;
    }

    public function setRoleName($roleName) {
        $this->roleName = $roleName;
    }
    public function setCustomPromos($customPromos)
    {
        $this->customPromos = $customPromos;
        return $this;
    }

    public function getCustomPromos()
    {
        return $this->customPromos;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function addPhoto($photo) {
        $this->photos[] = $photo;
        $photo->setClinic($this);
    }

    public function getPhotos() {
        return $this->photos;
    }
    /*  END GETTERS AND SETTERS */ 
    
    /* METHODS */
    public function getEmailaddress()
    {
        return $this->getRepEmail();
    }
    
    public function generateSalt()
    {
        $salt = rand(1, 100000);
        return $salt;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setLoginField()
    {
        if(!$this->repEmail) {
            throw new \Exception('No repEmail, cant make login for clinic');
        }
        $this->login = $this->getRepEmail();
    }

    /**
     * @param string $logoDir
     */
    public function setLogoDir($logoDir)
    {
        $this->logoDir = $logoDir;
    }

    /**
     * @return string
     */
    public function getLogoDir()
    {
        return $this->logoDir;
    }
    /**
     * This method processes the image file to create and save user avatar
     * You have $_FILES['logo'] superglobal in request in order to make
     * this function works
     *
     * @throws Exception on upload errors
     * @return bool True or false depending on the result
     */
    public function processLogo()
    {
        require_once('class.upload.php');
        $handle = new \upload($_FILES['logo']);

        // since this is avatar we want that every new
        // avatar will overwrite the old ones

        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');
        $log->debug('checking if logo file is uploaded');

        if ($handle->uploaded) {
            $log->debug('logo file uploaded');
            // original
            $handle->file_new_name_body = 'original';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $dir = $this->_generateDirectoryForLogo();
            $handle->process($config->clinics->logo->uploadDir . $dir);
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
            $handle->image_x        = $config->clinics->logo->sizes->big->width;
            $handle->image_y        = $config->clinics->logo->sizes->big->height;
            $handle->image_ratio_crop = true;
            $handle->process($config->clinics->logo->uploadDir . $dir);

            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // medium
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->clinics->logo->sizes->medium->width;
            $handle->image_y        = $config->clinics->logo->sizes->medium->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'medium';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->logo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }

            // small
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize   = true;
            $handle->image_x        = $config->clinics->logo->sizes->small->width;
            $handle->image_y        = $config->clinics->logo->sizes->small->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'small';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->logo->uploadDir . $dir);
            if ($handle->processed) {
                $handle->clean();
            } else {
                throw new \Exception('Upload errors: '.$handle->error);
            }
            $filename = $dir;
        } else {
            $log->debug('logo file is not uploaded: '.$handle->error);
            return false;
        }
        $this->setLogoDir($filename);
        return true;
    }

    public function deleteLogo()
    {
        // make sure if its ok to delete
        // delete all files in the photo folder
        $dir = $this->getLogoDir();
        if(!$dir) {
            throw new \Exception('No directory for files of photo object');
        }
        $config = \Zend_Registry::get('config');
        $dir = $config->clinics->logo->uploadDir . $dir;
        \Me\Common\Dir::rrmdir($dir);
        $this->setLogoDir(null);
        return $this;
    }

    protected function _generateDirectoryForLogo()
    {
        return $this->getId().'-'.$this->getSlug();
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    public function setFavoredByUsers($favoredByUsers)
    {
        $this->favoredByUsers = $favoredByUsers;
    }

    public function getFavoredByUsers()
    {
        return $this->favoredByUsers;
    }

    public function addFavoredByUser(\Trendmed\Entity\User $patient)
    {
        $this->favoredByUsers[] = $patient;
        return $this;
    }

    public function isFavoredByUser(\Trendmed\Entity\User $user)
    {
        return $this->favoredByUsers->contains($user);
    }

    public function removeFavoredByUser(\Trendmed\Entity\User $user)
    {
        $this->favoredByUsers->removeElement($user);
    }
}