<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Trendmed\Repository;
/**
 * This is clinic model.
 *
 * @ORM\Entity(repositoryClass="Trendmed\Repository\ClinicRepository")
 * @ORM\Table(name="clinics")
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic extends \Trendmed\Entity\User {
    
    public function __construct() {
        $this->services = new \Doctrine\Common\Collections\ArrayCollection;
        $this->wantBill = false;
        $this->country = 'Poland';
        $this->roleName = 'clinic';
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
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $geoLat;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $getLon;
    
    /**
     * @ORM\OneToMany(targetEntity="Trendmed\Entity\ClinicDescription", mappedBy="clinic")
     */
    protected $clinicDescription;
    
    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Service", mappedBy="clinic") 
     */
    protected $services;
    
    /**
     * @ORM\Column(type="string")
     * @var type 
     */
    protected $roleName;
    
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

    public function getClinicDescription() {
        return $this->clinicDescription;
    }

    public function setClinicDescription($clinicDescription) {
        $this->clinicDescription = $clinicDescription;
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

    /*  END GETTERS AND SETTERS */ 
    
    /* METHODS */
    public function getEmailladdress() 
    {
        return $this->getRepEmail();
    }
    
    public function generateSalt()
    {
        $salt = rand(1, 100000);
        return $salt;
    }
}