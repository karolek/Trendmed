<?php
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Maintnance of patients in the system
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class Admin_PatientsController extends Zend_Controller_Action {


    protected $_em; // entity manager od doctrine

    public function init()
    {
        /* Initialize action controller here */
        $this->_em =  $this->_helper->getEm();
    }

    /**
     * Displays list of clinics 
     */
    public function indexAction() {
        $this->view->headTitle('Lista zarejestrowanych pacjentów');
        $request = $this->getRequest();
        
        $qb = $this->_helper->getEm()->createQueryBuilder();
        $qb->select('p')
                ->from('\Trendmed\Entity\Patient', 'p')
                ->orderBy('p.created', 'DESC');
        $qb->setMaxResults(50);
        $qb->setFirstResult(0);
        
        $query = $qb->getQuery();
        
        
        $paginator = new Paginator($query, $fetchJoin = true);
        $this->view->paginator = $paginator;
    }

    public function changeActiveStateAction()
    {
        $request = $this->getRequest();
        $newState = $request->getParam('new-state');

        $clinic = $this->_fetchEntityFromParams();

        switch($newState) {
            case 1:
                $clinic->activate();
                $this->_helper->FlashMessenger(array(
                    'info' => 'Pacjent aktywowany'
                    )
                );
                break;
            case 0:
                $clinic->deactivate();
                $this->_helper->FlashMessenger(array(
                    'info' => 'Pacjent deaktywowany'
                    )
                );
                break;
            default:
                throw new \Exception('Undefined state given in '.__FUNCTION__);
                break;
        }
        $this->_em->persist($clinic);
        $this->_em->flush();

        $this->_helper->Redirector('index');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();

        $this->view->HeadTitle('Usuwanie pacjenta z portalu');

        $clinic = $this->_fetchEntityFromParams();

        $form       = new Admin_Form_DeletePatient();

        if($request->isPost()) {
            $this->_em->remove($clinic);
            $this->_em->flush();

            $this->_helper->FlashMessenger(array(
                'warning' => 'PAcjent o loginie: '. $clinic->getLogin(). ' została usunięta'
            ));
            $this->_helper->Redirector('index');
        }
        $this->view->user = $clinic;
        $this->view->form = $form;


    }

    /**
     * Fetches clinic by id param in get and returns it.
     *
     * @return \Trendmed\Entity\Patient
     * @throws Exception
     */
    protected function _fetchEntityFromParams()
    {
        $request = $this->getRequest();
        $repo = $this->_em->getRepository('\Trendmed\Entity\Patient');

        if($request->getParam('id')) {
            $userId    = $request->getParam('id');
            $user      = $repo->find($userId);
            if(!$user) throw new \Exception('No clinic found');
        } else {
            throw new \Exception('bad parameters given in '.__FUNCTION__);
        }
        return $user;
    }
}
?>
