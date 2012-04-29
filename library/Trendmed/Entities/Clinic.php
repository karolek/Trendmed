<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * This is clinic model.
 *
 * @ORM\Entity
 * @ORM\Table(name="clinics")
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Clinic extends \Me\User\Entity\User {
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
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $repEmail;
    
    /**
     * @ORM\Column(type="string");
     * @var type 
     */    
    protected $type;
    
    /**
     * @ORM\Column(type="string");
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
     * @ORM\OneToMany(targetEntity="Trendmed\Entity\ClinicDescription")
     */
    protected $clinicDescription;
    
    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Service") 
     */
    protected $services;
    
    /* END PROPERTIES */
    
    /*  GETTERS AND SETTERS */
    
    /*  END GETTERS AND SETTERS */ 
}