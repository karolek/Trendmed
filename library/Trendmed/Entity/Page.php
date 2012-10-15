<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;


/**
 * Description of Page
 * @ORM\Entity(repositoryClass="Trendmed\Repository\PagesRepository")
 * @ORM\Table(name="pages")
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Page extends \Me\Model\ModelAbstract
{

    public function __construct(array $options = null)
    {
        $this->created = new \DateTime;
        $this->modified = new \DateTime;
        $this->type = 'Text page';
        $this->isActive = true;
        $this->isSystemic = false;
        parent::__construct($options);
    }

    public static $pageTypes = array(
        'textpage' => 'Podstrona tekstowa',
        'article_normal' => 'Artykuł zwyczajny',
        'article_sponsored' => 'Artykuł sponsorowany'
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
     * @Gedmo\Slug(fields={"title"}, updatable=false, unique=true)
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Translatable
     */
    protected $content;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="boolean")
     * @var isSystemic state if this page is important from the system point of view if yes, than it can't be deleted by user
     */
    protected $isSystemic;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * @ORM\OneToOne(targetEntity="Trendmed\Entity\ArticlePhoto")
     */
    protected $leadPhoto;

    /**
     * @ORM\ManyToOne(targetEntity="Trendmed\Entity\Clinic")
     * @var one way relationship
     */
    protected $sponsoredByClinic;

    public function setSponsoredByClinic(\Trendmed\Entity\Clinic $clinic)
    {
        $this->sponsoredByClinic = $clinic;
    }

    public function getSponsoredByClinic()
    {
        return $this->sponsoredByClinic;
    }


    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setType($key)
    {
        if (!array_key_exists($key, self::$pageTypes)) {
            throw new \Exception('Page type: "' . $key . '" in not defined in ' . __CLASS__);
        }
        $this->type = $key;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTypeName()
    {
        return self::$pageTypes[$this->getType()];
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setActive($active)
    {
        $this->isActive = $active;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @ORM\PrePersist, @ORM\PreUpdate
     * @throws \Exception
     */
    public function validation()
    {
        if (!$this->title) {
            throw new \Exception('Page must have a title');
        }
        if (!$this->type) {
            throw new \Exception('Page must have a type');
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->modified = new \DateTime();
    }

    /**
     * @ORM\PreRemove
     * @throws \Exception
     */
    public function onRemove()
    {
        if ($this->isSystemic) throw new \Exception('This page is systemic and cannot be removed');
    }

    /**
     * @param \Trendmed\Entity\isSystemic $isSystemic
     */
    public function setIsSystemic($isSystemic)
    {
        $this->isSystemic = $isSystemic;
    }

    /**
     * @return \Trendmed\Entity\isSystemic
     */
    public function getIsSystemic()
    {
        return $this->isSystemic;
    }

    public function setLeadPhoto(\Trendmed\Entity\ArticlePhoto $photo)
    {
        $this->leadPhoto = $photo;
    }

    public function getLeadPhoto()
    {
        return $this->leadPhoto;
    }

    public function removeLeadPhoto()
    {
        $this->leadPhoto = null;
    }

}