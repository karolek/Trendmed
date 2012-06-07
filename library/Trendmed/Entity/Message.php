<?php
namespace Trendmed\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of User
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Message extends \Me\Model\ModelAbstract
{
    /* PROPERTIES */

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @var integer $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    protected $message;
    
    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Admin", mappedBy="messages")
     */
    protected $author;

    /**
     * @ORM\OneToMany(targetEntity="\Trendmed\Entity\Clinic", mappedBy="messages")
     */
    protected $clinicRecipient;

    /* END PROPERTIES */
    
    /* GETTERS & SETTERS */
    
    public function getId()
    {
        return $this->id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }


    /* END GETTERS & SETTERS */
    
    /* METHOS */
    
}