<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Category extends \Me\Model\ModelAbstract {
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
     * @var type 
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="integer")
     * @var type 
     */
    protected $depth;
    
    /**
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Category")
     */
    protected $parent;
}