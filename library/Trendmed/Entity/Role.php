<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Role extends \Me\Model\ModelAbstract {

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection;
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
    protected $users;
    
    /**
     * @ORM\Column(type="string")
     * @var type 
     */
    protected $name;

    /* END PROPERTIES */
    
    /* GETTERS AND STTERS */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function addToUser($user) {
        $this->users[] = $user;
    }

    /* END GETTERS AND SETTERS */
    
}