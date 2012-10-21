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

    public function __construct()
    {
        $this->childReservations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var \Trendmed\Entity\Reservation
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Reservation")
     */
    protected $parentReservation;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Reservation", mappedBy="parentGroupPromotion", cascade={"PERSIST"})
     */
    protected $childReservations;

    /**
     * @var array $discountValues copied discount values from clinic settings
     * @ORM\Column(type="array", nullable=false)
     *
     * @return \Trendmed\Entity\Reservation $reservation New reservation created for invitied patient
     */
    protected $discountValues;

    public function addPatient(\Trendmed\Entity\Patient $patient) {
        // make new reservation for each invited user up to limit
        if(!$this->parentReservation) {
            throw new \Exception('Group Reservation dont have parent reservation object setup in '.__FUNCTION__);
        }
        $reservation = $this->parentReservation;

        $childrenReservation = clone($reservation);
        $childrenReservation->setStatus('group_not_confirmed');
        $childrenReservation->setPatient($patient);
        $childrenReservation->setParentGroupPromotion($this);

        $this->childReservations->add($childrenReservation);

        return $childrenReservation;
    }

    public function getConfirmedCount()
    {
        // how many user did confirm the reservation
        $counter = 0;
        foreach($this->childReservations as $reservation) {
            if($reservation->status == 'confirmed') {
                $counter++;
            }
        }
        if($this->parentReservation->status == 'confirmed') {
            $counter++;
        }
        return $counter;
    }

    public function getTotalReservationsCount()
    {
        // +1 for parent reservation
        return count($this->childReservations) + 1;
    }

    public function getCurrentBonusRate()
    {

        if($this->discountValues[$this->getConfirmedCount()]) {
            return $this->discountValues[$this->getConfirmedCount()];
        }
        // checking if the array is not maxed out
        $values = $this->discountValues;
        krsort($values);
        foreach($values as $key => $value) {
            if($key <= $this->getConfirmedCount()) {
                return $value;
            }
        }
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
    public function setParentReservation(\Trendmed\Entity\Reservation $parentReservation)
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

    public function getReservations()
    {
        $collection = $this->getChildReservations();
        $collection->add($this->getParentReservation());
        return $collection;
    }


}