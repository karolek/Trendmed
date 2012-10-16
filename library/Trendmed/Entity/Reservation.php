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
        $this->billStatus = self::BILL_STATUS_NOT_PAID;
        $this->paymentHash = substr(md5(time).rand(1,99999), 2, 10);

        # we need to setup a date even if there was not any sendings
        $this->lastReminderAboutPaymentSend         = new \DateTime("01/01/1970");
        $this->lastReminderAboutSurveySend          = new \DateTime("01/01/1970");
        $this->lastReminderAboutReservationSend     = new \DateTime("01/01/1970");
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
            'patient'   => array('cancel', 'rate', 'getPdf')
        )),
        'new_date'  => array('name' => 'New date proposed', 'actions' => array(
            'clinic'    => array(),
            'patient' => array('confirmNewDate', 'discardNewDate')
        ))
    );

    const BILL_STATUS_NOT_WANTED = 2; # clinic does not require
    const BILL_STATUS_PAID = 1; # if payment has been done and was e' okey!
    const BILL_STATUS_NOT_PAID = 0; # for either not paid


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
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $billStatus;

    /**
     * @var \Trendmed\Entity\Clinic
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic", cascade={"persist"})
     */
    protected $clinic;

    /**
     * @var string  Used to double check payments request, could be seperate entity in future
     * @ORM\Column(type="string", nullable=false)
     */
    protected $paymentHash;

    /**
     * @var  \DateTime date and time of last reminder about reservation send
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastReminderAboutReservationSend;

    /**
     * @param \DateTime $lastReminderAboutReservationSend
     */
    public function setLastReminderAboutReservationSend($lastReminderAboutReservationSend)
    {
        $this->lastReminderAboutReservationSend = $lastReminderAboutReservationSend;
    }

    /**
     * @return \DateTime
     */
    public function getLastReminderAboutReservationSend()
    {
        return $this->lastReminderAboutReservationSend;
    }

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $amountOfReminderAboutReservationSend = 0;

    /**
     * @var  \DateTime date and time of last reminder about reservation send
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastReminderAboutPaymentSend;


    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $amountOfReminderAboutPaymentSend = 0;

    /**
     * @param int $amountOfReminderAboutPaymentSend
     */
    public function setAmountOfReminderAboutPaymentSend($amountOfReminderAboutPaymentSend)
    {
        $this->amountOfReminderAboutPaymentSend = $amountOfReminderAboutPaymentSend;
    }

    /**
     * @return int
     */
    public function getAmountOfReminderAboutPaymentSend()
    {
        return $this->amountOfReminderAboutPaymentSend;
    }

    /**
     * @param int $amountOfReminderAboutReservationSend
     */
    public function setAmountOfReminderAboutReservationSend($amountOfReminderAboutReservationSend)
    {
        $this->amountOfReminderAboutReservationSend = $amountOfReminderAboutReservationSend;
    }

    /**
     * @return int
     */
    public function getAmountOfReminderAboutReservationSend()
    {
        return $this->amountOfReminderAboutReservationSend;
    }

    /**
     * @param int $amountOfReminderAboutSurveySend
     */
    public function setAmountOfReminderAboutSurveySend($amountOfReminderAboutSurveySend)
    {
        $this->amountOfReminderAboutSurveySend = $amountOfReminderAboutSurveySend;
    }

    /**
     * @return int
     */
    public function getAmountOfReminderAboutSurveySend()
    {
        return $this->amountOfReminderAboutSurveySend;
    }

    /**
     * @param \DateTime $lastReminderAboutPaymentSend
     */
    public function setLastReminderAboutPaymentSend($lastReminderAboutPaymentSend)
    {
        $this->lastReminderAboutPaymentSend = $lastReminderAboutPaymentSend;
    }

    /**
     * @return \DateTime
     */
    public function getLastReminderAboutPaymentSend()
    {
        return $this->lastReminderAboutPaymentSend;
    }

    /**
     * @param \DateTime $lastReminderAboutSurveySend
     */
    public function setLastReminderAboutSurveySend($lastReminderAboutSurveySend)
    {
        $this->lastReminderAboutSurveySend = $lastReminderAboutSurveySend;
    }

    /**
     * @return \DateTime
     */
    public function getLastReminderAboutSurveySend()
    {
        return $this->lastReminderAboutSurveySend;
    }


    /**
     * @var \DateTime date and time of last reminder about making a survery after visit
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastReminderAboutSurveySend;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $amountOfReminderAboutSurveySend = 0;


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
        $rating->setReservation($this);
        $this->clinic->recountRating();
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

        # notification will be send only if proper template files exists in (APP/layouts/scripts
        $this->sendStatusNotification($status);

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
        $clinic->addReservation($this);
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

    /**
     * Composing PDF we have access to all nessesery data like $reservation object, clinic object, services object.
     *
     * @return \fpdf\FPDF
     */
    public function getPDF()
    {
		define('FPDF_FONTPATH', APPLICATION_PATH.'/../public/fonts/');
        $fpdf = new \fpdf\FPDF('P', 'mm', 'A4');
		$fpdf->AddFont('arialpl', '','arialpl.php');
		$fpdf->SetDrawColor(223, 223, 223); //color for borders
        $fpdf->AddPage();

		/* Logo */
		$logoFile = APPLICATION_PATH.'/../public/img/logo.png';
		$fpdf->image($logoFile);
		
		/* Reservation Number */
		$fpdf->ln(10);
        $fpdf->SetFont('arialpl', '', 35);
        $fpdf->Cell(0, 17, iconv('utf-8','iso-8859-2', $this->view->translate('Reservation') . ' #' . $this->id), 0, 1, 'C');
		$fpdf->SetFont('arialpl', '', 15);
		$fpdf->Cell(0, 9, iconv('utf-8','iso-8859-2', $this->patient->name), 0, 1, 'C');
		$fpdf->ln(20);
		
		/* Clinic Info */
		$fpdf->SetFont('arialpl', '', 30);
        $fpdf->Cell(0, 9, iconv('utf-8','iso-8859-2', $this->clinic->name), 0, 2);
		$fpdf->SetFont('arialpl', '', 15);
        $fpdf->Cell(0, 15, iconv('utf-8','iso-8859-2', $this->clinic->streetaddress).', '.iconv('utf-8','iso-8859-2', $this->clinic->postcode).' '.iconv('utf-8','iso-8859-2', $this->clinic->city), 0, 2);
		$fpdf->ln(15);

		/* Date of visit */
		$fpdf->SetFont('arialpl', '', 21);
        $fpdf->Cell(0, 12, iconv('utf-8','iso-8859-2', $this->view->translate('Date of visit: ')), 0, 2);
		$fpdf->SetFont('arialpl', '', 13);
		$fpdf->Cell(0, 9, $this->dateFrom->format("d-m-Y").' - '.$this->dateTo->format("d-m-Y"), 0, 2);
		$fpdf->ln(10);
		
		/* Services */
		$fpdf->SetFont('arialpl', '', 21);
        $fpdf->Cell(0, 12, iconv('utf-8','iso-8859-2', $this->view->translate('Services: ')), 0, 2);
		$fpdf->SetFont('arialpl', '', 13);		
		$i = 0;
		$len = count($this->services);
		foreach($this->services as $service) {
			if($i == $len-1) { 
				$fpdf->Cell(0, 9, iconv('utf-8','iso-8859-2', $service->category->name), 0, 2);
			} else {
				$fpdf->Cell(0, 9, iconv('utf-8','iso-8859-2', $service->category->name), 'B', 2);
			}
			$i++;
		}
		
		/* Footer */
		$fpdf->SetY(-30);
		$fpdf->SetTextColor(102, 102, 102);
		$fpdf->SetFont('arialpl', '', 12);
		$fpdf->Cell(0, 9, $this->view->translate('Generated on: ').date('d-m-Y'), 0, 1, 'C');
		
        # to access reservation data use #
        /**
         * $this->id; // reservation number
         * $this->services // reserved services (foreach them)
         * $this->clinic->name //clinic name
         * $this->clinic->city
         * $this->clinic->postcode
         * $this->clinic->province
         *
         */
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

    /**
     * @param int $billStatus
     */
    public function setBillStatus($billStatus)
    {
        $this->billStatus = $billStatus;
    }

    /**
     * @return int
     */
    public function getBillStatus()
    {
        return $this->billStatus;
    }

    public function sendStatusNotification($status)
    {
        $config = \Zend_Registry::get('config');
        $log = \Zend_Registry::get('log');


        $view = \Zend_Registry::get('view');
        $templatePath = APPLICATION_PATH . '/layouts/scripts/reservationNotifications';
        $view->addScriptPath($templatePath);
        $view->reservation = $this;

        $clinicTemplate = $templatePath.'/clinic/'.$status.'.phtml';
        $patientTemplate = $templatePath.'/patient/'.$status.'.phtml';

        if(file_exists($clinicTemplate)) {
            # sending notification to clinic
            $mail = new \Zend_Mail('UTF-8');
            $htmlContent = $view->render('clinic/' . $status.'.phtml'); // rendering a view template for content
            $mail->setBodyHtml($htmlContent);
            $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName); // setting FROM values from config
            $mail->addTo($this->clinic->getEmailaddress(), $this->clinic->getLogin());
            $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
            $subject = $config->siteEmail->clinic->firstPartReservation->subject . " " . $this->id . " " . $config->siteEmail->clinic->{$status.'Notification'}->subject;
            $mail->setSubject($subject);
            $mail->send();
            $log->debug('E-mail send to: ' . $this->clinic->getEmailaddress() . '
            from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
        }

        if(file_exists($patientTemplate)) {
            # sending notification to patient
            $mail = new \Zend_Mail('UTF-8');
            $htmlContent = $view->render('patient/' . $status.'.phtml'); // rendering a view template for content
            $mail->setBodyHtml($htmlContent);
            $mail->setFrom($config->siteEmail->fromAddress, $config->siteEmail->fromName);
            $mail->addTo($this->patient->getEmailaddress(), $this->patient->getLogin());
            $mail->addBcc($config->siteEmail->fromAddress, 'Redaktor Trendmed.eu'); //Adding copy for admin
            $subject = $config->siteEmail->patient->firstPartReservation->subject . " " . $this->id . " " . $config->siteEmail->patient->{$status.'Notification'}->subject;

            $mail->setSubject($view->translate($subject));
            $mail->send();
            $log->debug('E-mail send to: ' . $this->patient->getEmailaddress() . '
            from ' . $mail->getFrom() . ' subject: ' . $mail->getSubject());
        }

    }

    /**
     * @return string
     */
    public function getPaymentHash()
    {
        return $this->paymentHash;
    }


    /** END GETTERS AND SETTERS **/

}