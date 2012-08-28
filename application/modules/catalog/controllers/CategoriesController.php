<?php
/**
 * Controller takes care of displaying the clinics in categories
 * with pagination and sorting
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 *
 */
use Doctrine\ORM\Tools\Pagination\Paginator;

class Catalog_CategoriesController extends \Zend_Controller_Action
{

    protected $_em; //entity manager
    protected $_repo; // categories repository, for use of use

    public function init()
    {
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->_repo = $this->_em->getRepository('\Trendmed\Entity\Category');
    }

    public function viewAction()
    {
        $category = $this->_fetchCategoryFromParams();
        $this->view->category = $category;
        $this->view->headTitle($category->name);
        $this->view->selectedCategory = $category->slug;
        $this->_helper->_layout->setLayout('homepage');
        $config = \Zend_Registry::get('config');

        $request = $this->getRequest();
        $page = $request->getParam('page', 1);
        $order = $request->getParam('order', 'created');
        $type = $request->getParam('type', null);
        $direction = $request->getParam('direction', 'DESC');
        $this->view->city = $city = $request->getQuery('city', null);
        // for view we want the oposite direction for sorting links
        if ($direction == 'DESC') {
            $this->view->direction = 'ASC';
        } else {
            $this->view->direction = 'DESC';
        }

        // fetching data to paginator
        $qb = $this->_em->createQueryBuilder()
            ->select('s')
            ->from('\Trendmed\Entity\Service', 's')
            ->join('s.clinic', 'c')
            ->where('s.category = ?1');
        $qb->setParameter(1, $category->id);

        // adding filter type (just clinics / hospitals or small types or both)
        if ($type) {
            // fetching types of clinics for category selected by user
            $ors = array();
            foreach (\Trendmed\Entity\Clinic::getTypesForCategoryAsArray($type) as $key => $prop) {
                $ors[] = $qb->expr()->orx('c.type = ' . $qb->expr()->literal($prop['name']));
            }
            $qb->andWhere(join(' OR ', $ors));
        }

        // filtering by city
        if ($city) {
            $qb->andWhere($qb->expr()->eq('c.city', $qb->expr()->literal($city)));
        }

        // adding order by
        switch ($order) {
            case 'created':
                $qb->orderBy('s.created', $direction);
                break;
            case 'rating':
                $qb->orderBy('c.rating', $direction);
                break;
            case 'price':
                $qb->orderBy('s.pricemin', $direction);
                break;
            case 'popularity':
                $qb->orderBy('c.popularity', $direction);
                break;
            case 'name':
                $qb->orderBy('c.name', $direction);
                break;
            default:
                throw new \Exception('Undefined order given for sroting (' . $order . ')');
                break;
        }

        $pagination = new \Trendmed\Pagination($qb->getQuery(), $config->pagination->catalog->clinics, $page);

        $this->view->zendPaginator = $pagination->getZendPaginator();
        $this->view->numOfPages = $pagination->getPagesCount();
        $this->view->services = $pagination->getItems();
        $this->view->cities = $this->_em->getRepository('\Trendmed\Entity\Clinic')->findDistinctClinicCitiesAsArray();
        $this->view->headScript()->appendFile('/js/Catalog/view.js');

    }

    protected function _fetchCategoryFromParams()
    {
        $request = $this->getRequest();
        $slug = $request->getParam('slug');
        if (!$slug) throw new \Exception('No param given in ' . __FUNCTION__);
        // fetching the category
        $category = $this->_repo->findOneBySlug($slug);
        if (!$category) throw new \Exception('No category with slug: ' . $slug, 404);
        return $category;
    }

    public function serviceAction()
    {
        $this->_helper->_layout->setLayout('homepage');

        $request = $this->getRequest();
        $id = $request->getParam('id');

        $service = $this->_em->find('Trendmed\Entity\Service', $id);
        if(!$service) {
            throw new \Exception('No service with ID '.$id.' found');
        }

        $this->view->addScriptPath(APPLICATION_PATH . '/modules/clinic/views/scripts');
        $this->view->service = $service;
        $this->view->headTitle($this->view->translate('Service') .' '.$service->category->name);
    }

}

