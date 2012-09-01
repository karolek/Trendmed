<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Description of User
 *
 * @ORM\Table(name="reservations")
 * @ORM\Entity(repositoryClass="Trendmed\Repository\ReservationsRepository")
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
     * @var array List of allowed statuses for reservation.
     * Defined actions
     */
    protected static $_statuses = array(
        'new'       => array('name' => 'New', 'actions' => array( #what actions can be done now by who
            'clinic' => array('confirm', 'decline', 'newDate'),
            'patient' => array()
        )), #when reservations waits for clinic approval
        'closed'    => array('name' => 'Closed', 'actions' => array(
            'clinic' => array(),
            'patient' => array(),
        )),
        'confirmed' => array('name' => 'Confirmed', 'actions' => array(
            'clinic'    => array(),
            'patient'   => array('cancel', 'getPdf')
        )),
        'new_date'  => array('name' => 'New date proposed', 'actions' => array(
            'patient' => array('confirmNewDate', 'discardNewDate')
        ))
    );

    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime")
     */
    protected $dateFrom;

    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $alternativeDateFrom;


    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime")
     */
    protected $dateTo;


    /**
     * @var \DateTime From where the reservation can start
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $alternativeDateTo;


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

    /**
     * @var \Trendmed\Entity\Clinic
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic")
     */
    protected $clinic;

    protected function validate()
    {
        if (!$this->patient) {
            throw new \Exception('Reservation has to get patient object');
        }

        if (!$this->clinic) {
            throw new \Exception('Reservation has to get a clinic object');
        }

        if ($this->patient->isProfileFilled() < 1) {
            throw new \Exception('Patient who is reserving a visit must have filled his profile to full');
        }
    }

    protected $view;

    /** GETTERS AND SETTERS **/
    /**
     * @param string $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $dateFrom
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime $dateTo
     */
    public function setDateTo(\DateTime $dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param \Trendmed\Entity\Patient $patient
     */
    public function setPatient(\Trendmed\Entity\Patient $patient)
    {
        $this->patient = $patient;
    }

    /**
     * @return \Trendmed\Entity\Patient
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * @param \Trendmed\Entity\Payment $payment
     */
    public function setPayment(\Trendmed\Entity\Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \Trendmed\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param \Trendmed\Entity\Rating $rating
     */
    public function setRating(\Trendmed\Entity\Rating $rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return \Trendmed\Entity\Rating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $services
     */
    public function setServices($services)
    {
        $this->services[] = $services;
    }

    public function addService(\Trendmed\Entity\Service $service)
    {
        $this->services->add($service);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Controls logic of status changes of a reservation.
     * Based on new status things can happen to object.
     * @param string $status
     * @throws \Exception
     */
    public function setStatus($status)
    {
        if (!self::$_statuses[$status]) {
            throw new \Exception(
                'Given status (' . $status . ') does is not defined in '. __CLASS__
            );
        }
        # put Your logic here
        switch($status) {
            case 'confirmed':
                # if there was any alternative date and this reservation is confirmed than alternative date becomses
                # primary date
                if($this->newDateWasProposed()) {
                    $this->setDateFrom($this->getAlternativeDateFrom());
                    $this->setDateTo($this->getAlternativeDateTo());
                    $this->setAlternativeDateFrom(NULL);
                    $this->setAlternativeDateTo(NULL);
                }
                break;
            default:
                break;
        }
        $this->status = $status;
    }

    /**
     * @return Array
     */
    public function getStatusAsArray()
    {
        return self::$_statuses[$this->status];
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getClinic()
    {
        return $this->clinic;
    }

    public function setClinic(\Trendmed\Entity\Clinic $clinic)
    {
        $this->clinic = $clinic;
    }

    /**
     * @param \DateTime $alternativeDateFrom
     */
    public function setAlternativeDateFrom ($alternativeDateFrom)
    {
        $this->alternativeDateFrom = $alternativeDateFrom;
    }

    /**
     * @return \DateTime
     */
    public function getAlternativeDateFrom ()
    {
        return $this->alternativeDateFrom;
    }

    /**
     * @param \DateTime $alternativeDateTo
     */
    public function setAlternativeDateTo ($alternativeDateTo)
    {
        $this->alternativeDateTo = $alternativeDateTo;
    }

    /**
     * @return \DateTime
     */
    public function getAlternativeDateTo()
    {
        return $this->alternativeDateTo;
    }

    public function newDateWasProposed()
    {
        if ($this->getAlternativeDateFrom() != NULL) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getPDF()
    {
        $fpdf = new \fpdf\FPDF('P', 'mm', 'A4');
        $fpdf->AddPage();
        $fpdf->SetFont('Arial', '', 13);
        $fpdf->Cell(40, 20, $this->view->translate('Reservation') . ' #' . $this->id, 0, 1);
        $fpdf->Cell(80, 10, $this->view->translate('Clinic name').': '.$this->clinic->name.$fpdf->ln());
        $fpdf->Cell(80, 10, $this->view->translate('Clinic address').': '.$this->clinic->streetaddress.' '.$fpdf->clinic->city.$fpdf->ln());
        return $fpdf;
    }

    public function setView (\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function getView ()
    {
        return $this->view;
    }


    /** END GETTERS AND SETTERS **/

}