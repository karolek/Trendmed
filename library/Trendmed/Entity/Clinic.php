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

    public function __construct()
    {
        parent::__construct();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection;
        $this->wantBill = false;
        $this->country = 'Poland';
        $this->roleName = 'clinic';
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection;
        $this->favoredByUsers = new \Doctrine\Common\Collections\ArrayCollection;
        $this->popularity = 0;
        $this->rating = 0;
        $this->viewCount = 0;
        $this->isActive = true; # at start, all clinics are active
        $this->groupPromoEnabled = false;

    }

    public static $TYPES = array(
        'clinic' => array('name' => 'Klinika', 'category' => 'big'),
        'hospital' => array('name' => 'Szpital', 'category' => 'big'),
        'salon' => array('name' => 'Gabinet', 'category' => 'small'),
        'salon' => array('name' => 'Salon', 'category' => 'small'),
        'sanatorium' => array('name' => 'Sanatorium', 'category' => 'big'),
        'spa' => array('name' => 'Spa-wellness', 'category' => 'small')
    );

    /* PROPERTIES */

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $login;

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
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Service", mappedBy="clinic", cascade={"all"})
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $services;



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
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\ClinicPhoto", mappedBy="clinic", cascade={"all"})
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

    /**
     * @var int from 1 to 100 determines avg rating from users
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $rating;

    /**
     * @var int from 1 to n determines popularity amount based on resevations made divide by time (e.g. month)
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $popularity;

    /**
     * @var int amount of unique views on profile page
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $viewCount;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Reservation", mappedBy="clinic", cascade={"all"})
     */
    protected $reservations;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $groupPromoEnabled;

    protected $_welcomeEmailScript = 'register/_welcomeEmail.phtml';
    protected $_newPasswordScript = 'register/_newPassword.phtml';
    protected $_newEmailScript = 'profile/_newEmail.phtml';


    /* END PROPERTIES */
    /*  GETTERS AND SETTERS */

    public function isGroupPromoEnabled()
    {
        return TRUE === $this->groupPromoEnabled;
    }

    public function setGroupPromoEnabled($state)
    {
        $this->groupPromoEnabled = $state;
    }

    public function getId()
    {
        return $this->id;
    }


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function addView()
    {
        $this->viewCount++;
    }

    public function substractView()
    {
        $this->viewCount--;
    }

    public function getStreetaddress()
    {
        return $this->streetaddress;
    }

    public function setStreetaddress($streetaddress)
    {
        $this->streetaddress = $streetaddress;
    }

    public function getProvince()
    {
        $data = simplexml_load_file(APPLICATION_PATH . '/../data/regions.xml');
        $node = $data->xpath('//region[@id='.$this->province.']');
        return (string)$node[0];
    }

    public function setProvince($province)
    {
        $this->province = $province;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    public function getRepPhone()
    {
        return $this->repPhone;
    }

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

    public function setRepPhone($repPhone)
    {
        $this->repPhone = $repPhone;
    }

    public function getRepName()
    {
        return $this->repName;
    }

    public function setRepName($repName)
    {
        $this->repName = $repName;
    }

    public function getRepEmail()
    {
        return $this->repEmail;
    }

    public function setRepEmail($repEmail)
    {
        $this->repEmail = $repEmail;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTypeName()
    {
        return self::$TYPES[$this->type]['name'];
    }

    public function getTypeCategory()
    {
        return self::$TYPES[$this->type]['caegory'];
    }

    public static function getTypesForCategoryAsArray($category)
    {
        $return = array();
        foreach (self::$TYPES as $type) {
            if ($type['category'] == $category) {
                $return[] = $type;
            }
        }
        return $return;
    }

    public function setType($type)
    {
        if (!self::$TYPES[$type]) {
            throw new \Exception('Undefined clinic type: ' . $type);
        }
        $this->type = $type;
    }

    public function getWantBill()
    {
        return $this->wantBill;
    }

    public function setWantBill($wantBill)
    {
        $this->wantBill = $wantBill;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getGeoLat()
    {
        return $this->geoLat;
    }

    public function setGeoLat($geoLat)
    {
        $this->geoLat = $geoLat;
    }

    public function getGetLon()
    {
        return $this->getLon;
    }

    public function setGetLon($getLon)
    {
        $this->getLon = $getLon;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getActiveServices()
    {
        return $this->getServices()->filter(
            function($service) {
                if($service->isActive == true) {
                    return $service;
                }
            }
        );
    }

    public function setServices($services)
    {
        $this->services = $services;
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function addPhoto($photo)
    {
        $this->photos[] = $photo;
        $photo->setClinic($this);
    }

    public function getPhotos()
    {
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
                throw new \Exception('Upload errors: ' . $handle->error);
            }

            // big
            $handle->file_new_name_body = 'big';
            $handle->file_new_name_ext = 'jpg';
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize = true;
            $handle->image_x = $config->clinics->logo->sizes->big->width;
            $handle->image_y = $config->clinics->logo->sizes->big->height;
            $handle->image_ratio_crop = true;
            $handle->process($config->clinics->logo->uploadDir . $dir);

            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: ' . $handle->error);
            }

            // medium
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize = true;
            $handle->image_x = $config->clinics->logo->sizes->medium->width;
            $handle->image_y = $config->clinics->logo->sizes->medium->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'medium';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->logo->uploadDir . $dir);
            if ($handle->processed) {
                //$handle->clean();
            } else {
                throw new \Exception('Upload errors: ' . $handle->error);
            }

            // small
            $handle->file_overwrite = true; //and this is how we do it! :D
            $handle->file_auto_rename = false;
            $handle->image_resize = true;
            $handle->image_x = $config->clinics->logo->sizes->small->width;
            $handle->image_y = $config->clinics->logo->sizes->small->height;
            $handle->image_ratio_crop = true;
            $handle->file_new_name_body = 'small';
            $handle->file_new_name_ext = 'jpg';
            $handle->process($config->clinics->logo->uploadDir . $dir);
            if ($handle->processed) {
                $handle->clean();
            } else {
                throw new \Exception('Upload errors: ' . $handle->error);
            }
            $filename = $dir;
        } else {
            $log->debug('logo file is not uploaded: ' . $handle->error);
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
        if (!$dir) {
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
        return $this->getId() . '-' . $this->getSlug();
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
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

    /**
     * This callback is a function that will be executed before user persitance at register
     */
    public function beforeRegister()
    {
        $this->setLogin($this->getRepEmail());
    }

    /**
     * @param int $popularity
     */
    public function setPopularity($popularity)
    {
        $this->popularity = (int)$popularity;
    }

    /**
     * @return int
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        if ($rating > 100 or $rating < 0) {
            throw new \Exception('Rating can\'t be higher than 100 or lower than 0');
        }
        $this->rating = (int)$rating;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating * 10;
    }

    public function getViewCount()
    {
        return $this->viewCount;
    }

    public function addReservation(\Trendmed\Entity\Reservation $reservation)
    {
        $this->reservations->add($reservation);
    }

    public function getNewReservationsCount()
    {
        #filtering through reservations and finding only those with 'new' status
        return count($this->reservations->filter(
            function($reservation) {

                if($reservation->status == 'new') {
                    return $reservation;
                }
            }
        ));

    }

    /**
     * @ORM\PrePersist
     */
    public function recountRating()
    {
        $avg = array();
        foreach($this->getReviews() as $rating) {
            $avg[] = $rating->getAvgRate();
        }
        if(count($avg) > 0) {
            $this->rating = array_sum($avg) / count($avg);
        } else {
            $this->rating = 0;
        }

        # popularity is based on (rating + visists + reservations) / days registered in the system
        # lets count how many days clinic is in system
        $now = new \DateTime();
        $daysInSystem = $this->created->diff(new \DateTime('now'))->format("a"); # a = total amount of days
        if ($daysInSystem < 1) {
            $daysInSystem = 1;
        }
        $this->popularity = ($this->rating + $this->viewCount + count($this->reservations)) / $daysInSystem;
    }

    public function getReviews()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        if (count($this->reservations)) {
            foreach ($this->reservations as $reservation) {
                if($reservation->getRating()) {
                    $collection->add($reservation->getRating());
                }
            }
        }
        return $collection;
    }

    public function sendNewPasswordSetNotification($password)
    {

        $config = \Zend_Registry::get('config');
        $view = \Zend_Registry::get('view');
        $log = \Zend_Registry::get('log');
        $templatePath = APPLICATION_PATH . '/layouts/scripts/reservationNotifications';
        $view->addScriptPath($templatePath);

        # sending notification to clinic
        $mail = new \Zend_Mail('UTF-8');

        # passing password to view
        $view->password = $password;

        # setting up a mail object with content and config
        $htmlContent = $view->render('clinic/newPasswordSet.phtml'); // rendering a view template for content
        $mail->setBodyHtml($htmlContent);
        $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName); // setting FROM values from config
        $mail->addTo($this->getEmailaddress(), $this->getLogin());
        $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
        $subject = $config->siteEmail->clinic->newPasswordSet->subject;
        $mail->setSubject($subject);
        $mail->send();
        $log->debug('E-mail send to: ' . $this->getEmailaddress() . '
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
    }

    /**
     * Checks if clinic allready used given $category by adding a service to it
     *
     * @param Category $category
     * @return bool
     */
    public function hasServiceInCategory(\Trendmed\Entity\Category $category)
    {
        if ($this->services) {
            foreach($this->services as $service) {
                if($service->category->id == $category->id) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return collection of already used categories
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function usedCategories()
    {
        $categories = new \Doctrine\Common\Collections\ArrayCollection();
        if (count($this->services) > 0) {
            foreach ($this->services as $service) {
                $categories->add($service->category);
            }
        }

        return $categories;
    }


    public function recommendedBy($recomendingEmail, $recommendToEmail)
    {
        $config = \Zend_Registry::get('config');
        $view = \Zend_Registry::get('view');
        $log = \Zend_Registry::get('log');
        $templatePath = APPLICATION_PATH . '/layouts/scripts/reservationNotifications';
        $view->addScriptPath($templatePath);

        # sending notification to clinic
        $mail = new \Zend_Mail('UTF-8');

        # passing password to view
        $view->password = $password;

        # setting up a mail object with content and config
        $htmlContent = $view->render('clinic/recommend.phtml'); // rendering a view template for content
        $mail->setBodyHtml($htmlContent);
        $mail->setFrom($recomendingEmail, $recomendingEmail); // setting FROM values from config
        $mail->addTo($recommendToEmail, $recommendToEmail);
        $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
        $subject = $view->translate($config->siteEmail->clinic->recommend->subject);
        $mail->setSubject($subject);
        $mail->send();
        $log->debug('E-mail send to: ' . $mail->getRecipients() . '
        from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
    }

}