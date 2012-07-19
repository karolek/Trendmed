<?php
namespace Trendmed\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Description of User
 * @ORM\Entity
 * @ORM\Table(name="article_photos")
 * @ORM\HasLifecycleCallbacks
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ArticlePhoto extends \Trendmed\Entity\AbstractPhoto
        implements \Trendmed\Interfaces\Photo
{

    protected $_photoTypeName = 'ArticlePhoto';

    /**
     * @ORM\OneToOne(targetEntity="Trendmed\Entity\Page")
     */
    protected $article;

    public function setArticle(\Trendmed\Entity\Page $article)
    {
        $this->article = $article;
        $article->setLeadPhoto($this);
    }

    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @ORM\PreRemove
     */
    public function detachFromArticle()
    {
        $this->article->removeLeadPhoto();
        $this->article = null;
    }
}