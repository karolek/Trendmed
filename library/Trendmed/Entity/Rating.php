<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="rateings")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Rating extends  \Me\Model\ModelAbstract {
    /* PROPERTIES */
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="priceRate", type="integer", nullable=false)
     * @var integer 1-10 rateing of price due to service
     */
    protected $priceRate;

    /**
     * @ORM\Column(name="serviceRate", type="integer", nullable=false)
     * @var integer 1-10 rateing of serivce
     */
    protected $serviceRate;

    /**
     * @var integer 1-10 rating of stuff in clinic
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $stuffRate;

    /**
     * @var float average of all other rateing, counteted automaticly
     * @ORM\Column(type="float", nullable=false)
     */
    protected $avgRate;

    /**
     * @var text additional comments to survey by patient
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var \Trendmed\Entity\Reservation connected reservation
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Reservation")
     */
    protected $reservation;

    ## GETTERS AND SETTERS ##

    /**
     * @param \Trendmed\Entity\text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return \Trendmed\Entity\text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $priceRate
     */
    public function setPriceRate($priceRate)
    {
        $this->priceRate = $priceRate;
    }

    /**
     * @return int
     */
    public function getPriceRate()
    {
        return $this->priceRate;
    }

    /**
     * @param int $serviceRate
     */
    public function setServiceRate($serviceRate)
    {
        $this->serviceRate = $serviceRate;
    }

    /**
     * @return int
     */
    public function getServiceRate()
    {
        return $this->serviceRate;
    }

    /**
     * @param int $stuffRate
     */
    public function setStuffRate($stuffRate)
    {
        $this->stuffRate = $stuffRate;
    }

    /**
     * @return int
     */
    public function getStuffRate()
    {
        return $this->stuffRate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Use addRating() method on reservation instead of this, to not get cought in infinite loop
     * @param Reservation $reservation
     * @return Rating
     */
    public function setReservation(\Trendmed\Entity\Reservation $reservation)
    {
        $this->reservation = $reservation;
        return $this;
    }

    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->avgRate = (
            (int) $this->getPriceRate() + (int) $this->getServiceRate() + (int) $this->getStuffRate()
        ) / 3;
    }

    /**
     * @return float
     */
    public function getAvgRate()
    {
        return $this->avgRate;
    }
}