<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 *
 * @ORM\Table(name="services")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Service extends \Me\Model\ModelAbstract {

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->isactive = true;
        $this->viewcount = 0;
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="Trendmed\Entity\ServicePhoto", mappedBy="service", cascade="delete")
     *
     */
    protected $photos;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Translatable
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="float")
     * @var string
     */
    protected $pricemin;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $pricemax;

    /**
     * @ORM\Column(type="integer")
     * @var string
     */
    protected $viewcount;

    /**
     * @ORM\Column(type="boolean")
     * @var string
     */
    protected $isactive;

    /**
     * @ORM\Column(type="datetime")
     * @var string
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var string
     */
    protected $modified;

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic", inversedBy="services")
     */
    protected $clinic;

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Category", inversedBy="services")
     */
    protected $category;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @throws \Exception on validation error
     */
    public function validate()
    {
        if(!$this->description) throw new \Exception('Service should have a description');
        if(!$this->clinic) throw new \Exception('Service should have a clinic');
        if(!$this->category) throw new \Exception('Service should have a category');
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception on validation error
     */
    public function onUpdate()
    {
        $this->modified = new \DateTime();
    }

    public function setCategory(\Trendmed\Entity\Category $category)
    {
        $category->addService($this);
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setClinic(\Trendmed\Entity\Clinic $clinic)
    {
        $this->clinic = $clinic;
    }

    public function getClinic()
    {
        return $this->clinic;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $isactive
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;
    }

    /**
     * @return string
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

    /**
     * @param string $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return string
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param string $pricemax
     */
    public function setPricemax($pricemax)
    {
        $this->pricemax = $pricemax;
    }

    /**
     * @return string
     */
    public function getPricemax()
    {
        return $this->pricemax;
    }

    /**
     * @param string $pricemin
     */
    public function setPricemin($pricemin)
    {
        $this->pricemin = $pricemin;
    }

    /**
     * @return string
     */
    public function getPricemin()
    {
        return $this->pricemin;
    }

    /**
     * @param string $viewcount
     */
    public function setViewcount($viewcount)
    {
        $this->viewcount = $viewcount;
    }

    /**
     * @return string
     */
    public function getViewcount()
    {
        return $this->viewcount;
    }

    public function toArray()
    {
        $arr = array(
            'categories'    => $this->category->getId(),
            'description'   => $this->description,
            'pricemin'      => $this->pricemin,
            'pricemax'      => $this->pricemax,

        );
        return $arr;
    }

    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    public function addPhoto(\Trendmed\Entity\ServicePhoto $photo)
    {
        $this->photos[] = $photo;
        $photo->setService($this);
    }


}