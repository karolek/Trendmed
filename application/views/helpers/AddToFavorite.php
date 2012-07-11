<?php
class Trendmed_View_Helper_AddToFavorite extends Zend_View_Helper_Abstract
{
    public $view;
    protected $_rootNode;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function scriptPath($script)
    {
        return $this->view->getScriptPath($script);
    }

    public function AddToFavorite($entity, $text = 'Add to favorite')
    {
        // add javascript to make a ajax request
        $this->view->headScript()->appendFile('/js/general.js');
        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $linkUrl = $this->view->url(array(
            'action'        => 'add-favorite-clinic',
            'controller'    => 'profile',
            'module'        => 'patient',
            'entity_id'     => $entity->getId(),
        ), 'default', true);

        $entityName = $em->getClassMetadata(get_class($entity))->name;

        if($this->view->LoggedUser()) {
            // set up class, fav or not fav
            if($entity->isFavoredByUser($this->view->LoggedUser())) {
                $class = 'unfav';
            } else {
                $class = 'fav';
            }
        } else { // user not logged, making special LoggedLink
            return $this->view->LoggedLink($text, array());
        }

        $output = '<a entity="' . $entityName . '" href="' . $linkUrl . '" class="'. $class .' add-to-fav">' . $text . '</a>';

        return $output;
    }

}