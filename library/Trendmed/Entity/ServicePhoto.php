<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 *
 * @ORM\Table(name="service_photos")
 * @ORM\Entity
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ServicePhoto extends \Trendmed\Entity\AbstractPhoto
implements \Trendmed\Interfaces\Photo
{
    protected $_photoTypeName = 'services';

    /**
     * @ORM\ManyToOne(targetEntity="Trendmed\Entity\Service", inversedBy="photos")
     */
    protected $service;

    public function setService(\Trendmed\Entity\Service $service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        return $this->service;
    }
    /* END METHODS */
}