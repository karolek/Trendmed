<?php
namespace IAA\Trendmed;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* Description of Page
* @ORM\Entity
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
        parent::__construct($options);
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
     * @Gedmo\Slug(fields={"title"}, updatable=false, unique=true)
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
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

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
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


    /**
     * @ORM\PrePersist, @ORM\PreUpdate
     * @throws \Exception
     */
    public function validation()
    {
        if(!$this->title) {
            throw new \Exception('Page must have a title');
        }
        if(!$this->type) {
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
}