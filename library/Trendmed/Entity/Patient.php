<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="patients")
 * @ORM\Entity(repositoryClass="Trendmed\Repository\PatientRepository")
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Patient extends \Trendmed\Entity\User
{
    public function __construct() {
        $this->roleName = 'patient';
        $this->favoriteClinics = new \Doctrine\Common\Collections\ArrayCollection;
        $this->isNewsletterActive = false;
        $this->isActive = true;
        $this->isTemp = false;

        parent::__construct();
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
     * @ORM\ManyToMany(targetEntity="Trendmed\Entity\Clinic", inversedBy="favoredByUsers")
     * @var \Doctrine\Common\Collections\ArrayCollection one way connection
     */
    protected $favoriteClinics;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string $name User real name
     */
    protected $name;

    /**
     * @var string $title One of user titles like (Mr, Ms, Dr and such)
     * @ORM\Column(type="string", nullable=true);
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string $country User real country name
     */
    protected $country;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string $facebookId User facebook account ID (if connected)
     */
    protected $facebookId;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string $phoneNumber User real phone number
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var type
     */
    protected $isNewsletterActive;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var bool was user invited from group promotion (and dint have account before)
     */
    protected $isTemp;

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $reservations
     */
    public function setReservations($reservations)
    {
        $this->reservations = $reservations;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Reservation", mappedBy="patient", cascade={"all"})
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $reservations;

    /* GETTERS AND SETTERS */
    public function getId() {
        return $this->id;
    }

    protected $_welcomeEmailScript  = 'register/_welcomeEmail.phtml';
    protected $_newPasswordScript   = 'register/_newPassword.phtml';
    protected $_newEmailScript      = 'profile/_newEmail.phtml';
    protected $_moduleName = 'patient';

    /**
     * @param Clinic $clinic
     */
    public function addFavoriteClinic(\Trendmed\Entity\Clinic $clinic)
    {
        $this->favoriteClinics[] = $clinic;
        $clinic->addFavoredByUser($this);
    }

    public function removeFavoriteClinic(\Trendmed\Entity\Clinic $clinic)
    {
        $this->favoriteClinics->removeElement($clinic);
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

    /**
     * @param Clinic $clinic
     * @return string "unlike" if user was unlinking the clinic, "like" if user was liking
     */
    public function toggleFavoriteClinic(\Trendmed\Entity\Clinic $clinic)
    {
        if($this->isFavoringClinic($clinic)) {
            $result = 'unlike';
            $this->removeFavoriteClinic($clinic);
        } else {
            $result = 'like';
            $this->addFavoriteClinic($clinic);
        }
        return $result;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {                $this->_persistUser(0);

        return $this->phoneNumber;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setEmailAddress($email)
    {
        $this->setLogin($email);
    }

    public function isNewsletterActive() {
        return $this->isNewsletterActive;
    }

    public function setIsNewsletterActive($isNewsletterActive) {
        $this->isNewsletterActive = $isNewsletterActive;
    }

    /**
     * Returns -1 when profile is even touched, 0 if profile somewhat edited and 1 if profile is completed
     */
    public function isProfileFilled()
    {
        $result = -1; #not filled at all
        $amountFilled = 0;
        if (!empty($this->name)) {
            $amountFilled++;
        }

        if (!empty($this->country)) {
            $amountFilled++;
        }

        if (!empty($this->title)) {
            $amountFilled++;
        }

        if (!empty($this->phoneNumber)) {
            $amountFilled++;
        }

        if($amountFilled >= 4) {
            $result = 1;
        } elseif($amountFilled > 0) {
            $result = 0;
        }
        return $result;
    }

    /**
     * @param boolean $isTemp
     */
    public function setIsTemp($isTemp)
    {
        $this->isTemp = $isTemp;
    }

    /**
     * @return boolean
     */
    public function getIsTemp()
    {
        return $this->isTemp;
    }

    /**
     * Callback, before persisting model in registration (and only then)
     */
    public function beforeRegister()
    {
        // be sure to activate the user
        $this->setIsActive(true);
    }

    /**
     * This user authorizes (checks) user via Facebook service.
     * If given Facebook Id is correct for this object and confirmed by logged user on Facebook than
     * I will persist that user in session
     *
     * @param $facebookId integer Facebook user Id
     * @return bool true on valid credentail, false on not valid
     */
    public function authorizeViaFacebook($facebookId)
    {
        if($facebookId == $this->getFacebookId()) {
            // now, we'r gonna use facebook SDK to verify if user is logged into facebook
            // initializing the Facebook API
            $config = \Zend_Registry::get('config');
            $fbConfig = array();
            $fbConfig['appId']  = $config->facebook->appId;
            $fbConfig['secret'] = $config->facebook->aapSecret;
            $fbConfig['fileUpload'] = false; // optional
            $facebook = new \Facebook($fbConfig);
            $uid = $facebook->getUser();
            if ($uid == $facebookId) {
                $result = true;
                $arrayToStore = array(
                    'id' => $this->getId(),
                    'roleName' => $this->getRole()->name,
                    'entityNamespace' => get_class(),
                );
                $this->setLastLoginTime(new \DateTime());
                $auth = \Zend_Auth::getInstance();

                $auth->getStorage()->write($arrayToStore); // saveing user.id to session to use by
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

}