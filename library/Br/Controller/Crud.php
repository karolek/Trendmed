<?php
/**
 * @author Bartosz Rychlicki <b@br-design.pl>
 */
abstract class Br_Controller_Crud extends \Br_Controller_Action {

    /**
     * @var string Full namespace of the Zend_Form object for this controller
     */
    protected $_resourceFormClass           = null;

    /**
     * @var string full namespace of the entity class
     */
    protected $_resourceEntityClass         = null;

    /**
     * @var object repository object for given entity
     */
    protected $_repo                        = null;

    /**
     * @var \Zend_Form Form for this resource, initialize by init() method
     */
    protected $_form                        = null;

    /**
     * Message if validation of the form fails
     */
    const MSG_VALIDATION_ERROR              = 'Please fix all errors in the form';

    /**
     * Message if save of the entity is success
     */
    const MSG_SUCCESS_SAVE                  = 'Your changes has been saved';
    const MSG_DELETE                        = 'Record deleted';

    const RESOURCE_ORDER_FIELD              = 'created';
    const RESOURCE_ORDER_DIRECTION          = 'DESC';

    public function init()
    {
        parent::init();

        // checking if all parameters are set
        if(!$this->_resourceFormClass) {
            throw new \Exception(
                'No resource form class given in ' . __FUNCTION__ . ' parameter, set _resourceFormClass variable
            ');
        } else { // checking if  form class exists
            if (!class_exists($this->_resourceFormClass)) {
                throw new \Exception(
                    'Given form class ' . $this->_resourceFormClass . ' could not be found'
                );
            }
        }
        if(!$this->_resourceEntityClass) {
            throw new \Exception(
                'No resource entity class name given in ' . __FUNCTION__ . ' set _resourceEntityClass variable'
            );
        } else {
            if (!class_exists($this->_resourceEntityClass)) {
                throw new \Exception(
                    'Given entity class ' . $this->_resourceEntityClass . ' could not be found'
                );
            }
        }
        // checking if model have implemented proper interface
        $entity = new $this->_resourceEntityClass;
        if (!$entity instanceof Br_Model_Interface_IsCrudable) {
            throw new \Exception(
                'Given entity ' . $this->_resourceEntityClass . ' should implement Br_Model_Interface_IsCrudable'
            );
        }
        // checking if doctrine manager is present
        if (!is_object($this->_em) or !$this->_em instanceof Doctrine\ORM\EntityManager) {
            throw new \Exception('No proper doctrine entity manager found in ' . __FUNCTION__);
        }
        // fething the repo
        $this->_repo = $this->_em->getRepository($this->_resourceEntityClass);

        // checking if form has an ID field
        $form = new $this->_resourceFormClass();
        if (!$form->getElement('id')) {
            throw new \Exception(
                $this->_resourceFormClass . ' should have a "id" element for modify action defined'
            );
        }
        $this->_form = $form;

    }

    public function indexAction()
    {
        $entities = $this->_fetchList();
        $this->view->entities = $entities;
    }

    public function saveAction()
    {
        $request    = $this->getRequest();
        // if ID param is present in the request than this is an update action
        $id         = $request->getParam('id', null);

        if ($id > 0) {
            // fetching entity
            $entity = $this->_repo->find($id);
            if (!$entity) throw new \Exception('No ' . $this->_resourceEntityClass . ' by ID ' . $id . ' found');
            $this->_populateForm($this->_form, $entity);
        } else {
            // new entity
            $entity = new $this->_resourceEntityClass();
        }

        if ($request->isPost()) {
            $post = $request->getPost();
            if ($this->_form->isValid($post)) {
                $values = $this->_form->getValues();
                $entity->setOptions($values);
                $this->_em->persist($entity);
                $this->_em->flush();
                $this->_helper->FlashMessenger(array('success' => self::MSG_SUCCESS_SAVE));
                $this->_helper->Redirector('index');
            } else {
                $this->_helper->FlashMessenger(array('warning' => self::MSG_VALIDATION_ERROR));
            }
        }

        $this->view->form = $this->_form;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $id      = $request->getParam('id', null);
        $entity = $this->_repo->find($id);
        if (!$entity) throw new \Exception('No ' . $this->_resourceEntityClass . ' by ID ' . $id . ' found');
        $this->_em->remove($entity);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('info' => self::MSG_DELETE));
        $this->_helper->Redirector('index');
    }

    public function activateAction()
    {
        $request = $this->getRequest();
        if($request->getParam('id')) {
            $entityId   = $request->getParam('id');
            $entity     = $this->_repo->find($entityId);
            if(!$entity) throw new \Exception('No entity found');

            $entity->setActive(!$entity->isActive());
            $this->_em->persist($entity);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array(
                'success' => 'Active state changed'
            ));
            $this->_helper->Redirector('index');
        } else {
            throw new \Exception('ID to delete not given');
        }
    }

    protected function _fetchList()
    {
        // query construct
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from($this->_resourceEntityClass, 'e');

        if(self::RESOURCE_ORDER_FIELD) {
            $qb->orderBy('e.'.self::RESOURCE_ORDER_FIELD, self::RESOURCE_ORDER_DIRECTION);
        }

        return $qb->getQuery()->getResult();
    }

    protected function _populateForm(\Zend_Form $form, $entity)
    {
        return $form->populate($entity->toArray());
    }
}
