<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
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
        $this->viewcount = 0;
        $this->created = new \DateTime();
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
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $pricemin;

    /**
     * @ORM\Column(type="string")
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
     * @ORM\Column(type="datetime")
     * @var string
     */
    protected $modified;
}