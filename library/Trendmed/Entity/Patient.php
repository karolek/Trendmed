<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="patients")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Patient extends \Trendmed\Entity\User
{
    public function __construct() {
        $this->roleName = 'patient';
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
    
    // TODO: Move to parent class
    /**
     * @ORM\Column(type="string")
     * @var type 
     */
    protected $roleName;

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic", inversedBy="favoredByUsers")
     * @var \Doctrine\Common\Collections\ArrayCollection one way connection
     */
    protected $favoriteClinics;

    /* GETTERS AND SETTERS */
    public function getId() {
        return $this->id;
    }

    public function getRoleName() {
        return $this->roleName;
    }

    public function setRoleName($roleName) {
        $this->roleName = $roleName;
    }

    protected $_welcomeEmailScript = 'register/_welcomeEmail.phtml';


    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $favoriteClinics
     */
    public function addFavoriteClinic(\Trendmed\Entity\Clinic $favoriteClinics)
    {
        $this->favoriteClinics[] = $favoriteClinics;
        $favoriteClinics->addFavoredByUser($this);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFavoriteClinics()
    {
        return $this->favoriteClinics;
    }

    public function isFavoringClinic(\Trendmed\Entity\Clinic $clinic)
    {
        return $this->favoriteClinics->contains($clinic);
    }

    public function getEmailaddress()
    {
        return $this->login;
    }


}