<?php
use Doctrine\Common\Util\Debug as Debug;
/**
 * This is a view helper for fetching and displaying
 * popular tags of projects.
 *
 * @author Bartosz Rychlicki
 */
class Trendmed_View_Helper_DisplayAd extends Zend_View_Helper_Abstract
{

    public $view;
    protected $_em;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function displayAd($zone)
    {
        if(!$this->_em) {
            $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        }
        $config = \Zend_Registry::get('config');
        $repo = $this->_em->getRepository('\Trendmed\Entity\BannerAd');
        $ad = $repo->findOneToShowNextForzone($zone);
        if(!$ad) return;
        switch($ad->type) {
            case 'static':
                $width = $config->ads->zone->$zone->width;
                $height = $config->ads->zone->$zone->height;
                $output = '<img width="' . $width . '" height="' . $height . '" src="' . $config->ads->publicDir . '/' . $ad->file . '/original.jpg" />';
                if($ad->target) {
                    $output = '<a target="' . $ad->openIn . '" href="/ad-redirect/' . $ad->file . '">' . $output . '</a>';
                }
                break;
            default:
                throw new \Exception('Given ('.$ad->type.') banner type not defined in '.__FUNCTION__);
        }
        $ad->viewCount++;
        $ad->shown = true;
        $this->_em->persist($ad);
        $this->_em->flush();
        return $output;
    }
}