<?php
namespace Trendmed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Description of User
 *
 * @ORM\Table(name="categories")
 * @Gedmo\Tree(type="nested")
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Category extends \Me\Model\ModelAbstract implements \Gedmo\Tree\Node
{

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->serviceCount = 0;
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
     * @ORM\Column(type="string")
     * @Gedmo\Translatable
     * @var string
     */
    protected $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Translatable
     * @var type 
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     * @var type 
     */
    protected $created;

    /**
     * @ORM\Column(type="integer")
     * @var type 
     */
    protected $serviceCount;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /** END PROPERTIES  **/
    /** GETTERS & SETTERS **/

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return the $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return the $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return the $serviceCount
     */
    public function getServiceCount()
    {
        return $this->serviceCount;
    }

    /**
     * @return the $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \Trendmed\Entity\type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param \Trendmed\Entity\type $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param \Trendmed\Entity\type $serviceCount
     */
    public function setServiceCount($serviceCount)
    {
        $this->serviceCount = $serviceCount;
    }

    /**
     * @param field_type $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getSlug()
    {
        return $this->slug;
    }
    
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}