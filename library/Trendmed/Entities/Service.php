<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Patient {
        /* PROPERTIES */
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}