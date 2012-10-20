<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="group_reservations")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class GroupReservation extends \Me\Model\ModelAbstract {
    /* PROPERTIES */
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Trendmed\Entity\Reservation
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Reservation")
     */
    protected $parentReservation;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Reservation")
     */
    protected $childReservations;

    /**
     * @var array $discountValues copied discount values from clinic settings
     * @ORM\Column(type="array", nullable=false)
     */
    protected $discountValues;

    public function inviteByEmail($email) {
        // make new reservation for each invited user up to limit
        // send e-mail notification to user
    }

    public function getConfirmedCount()
    {
        // how many user did confirm the reservation
    }

    /**
     * @param \Trendmed\Entity\Reservation $childReservations
     */
    public function setChildReservations($childReservations)
    {
        $this->childReservations = $childReservations;
    }

    /**
     * @return \Trendmed\Entity\Reservation
     */
    public function getChildReservations()
    {
        return $this->childReservations;
    }

    /**
     * @param \Trendmed\Entity\Reservation $parentReservation
     */
    public function setParentReservation($parentReservation)
    {
        $this->parentReservation = $parentReservation;
        // copying discount values
        $this->discountValues = $parentReservation->getClinic()->getGroupPromoDiscounts();
        if(empty($this->discountValues)) {
            throw new \Exception('discount values for group reservation in clinic are not present');
        }
    }

    /**
     * @return \Trendmed\Entity\Reservation
     */
    public function getParentReservation()
    {
        return $this->parentReservation;
    }



}