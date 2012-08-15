<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="reservations")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Reservation extends  \Me\Model\ModelAbstract {

    public function __construct()
    {
        $this->status   = 'new';
        $this->created  = new \DateTime();
        $this->services = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @var string fixed status of reservation
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @var array List of allowed statuses for reservation
     */
    protected static $_statuses = array(
        'new'       => array('name' => 'New'),
        'closed'    => array('name' => 'Closed'),
    );

    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime")
     */
    protected $dateFrom;

    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime")
     */
    protected $dateTo;

    /**
     * @var string question the patient has to clinic making the new reservation
     * @ORM\Column(type="string", nullable=true)
     */
    protected $question;

    /**
     * @var string Clinics anwser to patient question, can be overwritten
     * @ORM\Column(type="string", nullable=true)
     */
    protected $answer;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \Trendmed\Entity\Patient Patient that makes a reservation
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Patient")
     */
    protected $patient;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection List of services the patient want's durning the reservation
     * @ORM\ManyToMany(targetEntity="\Trendmed\Entity\Service")
     */
    protected $services;

    /**
     * @var \Trendmed\Entity\Rating Rating field by patient after the visist
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Rating")
     */
    protected $rating;

    /**
     * @var \Trendmed\Entity\Payment
     * @ORM\OneToOne(targetEntity="\Trendmed\Entity\Payment")
     */
    protected $payment;

    protected function validate()
    {
        if (!$this->patient) {
            throw new \Exception('Reservation has to get patient object');
        }

        if (!$this->clinic) {
            throw new \Exception('Reservation has to get a clinic object');
        }
    }
}