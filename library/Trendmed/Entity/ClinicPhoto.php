<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 *
 * @ORM\Table(name="clinic_photos")
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicPhoto extends \Trendmed\Entity\AbstractPhoto
implements \Trendmed\Interfaces\Photo
{
    /* PROPERTIES */

    protected $_photoTypeName = 'clinics';

    /**
     * @ORM\ManyToOne(targetEntity="\Trendmed\Entity\Clinic", inversedBy="photos")
     * @Gedmo\SortableGroup
     */
    protected $clinic;

    /**
     * @param  $clinic
     */
    public function setClinic($clinic)
    {
        $this->clinic = $clinic;
    }

    /**
     * @return
     */
    public function getClinic()
    {
        return $this->clinic;
    }

    /* END METHODS */

}