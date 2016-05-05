<?php
namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Accantonato;
use Accantona\Form\AccantonatoForm;
use Zend\Debug\Debug;

class AccantonatoController extends AbstractActionController
{

    protected $user;
    protected $accantonatoTable;

    public function indexAction()
    {
        $form = new AccantonatoForm();
        $request = $this->getRequest();
        $user = $this->getUser();

        if ($request->isPost()) {

            $accantonato = new Accantonato();
            $form->setInputFilter($accantonato->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $user->id;
                $accantonato->exchangeArray($data);
                $this->getAccantonatoTable()->save($accantonato);
                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }

        $where = array('userId=' . $user->id);
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'valuta>"' . date('Y-m-d', strtotime("-$months month")) . '"';
        }

        return new ViewModel(array(
            'months' => $months,
            'rows' => $this->getAccantonatoTable()->fetchAll($where),
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $user = $this->getUser();

        $spend = $em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $user->id));

        if (!$spend) {
            return $this->redirect()->toRoute('accantona_accantonato', array('action' => 'index'));
        }

        $form = new AccantonatoForm('accantonati');
        $form->bind($spend);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($spend->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }

        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $spend = $em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $this->getUser()->id));

        if ($spend) {
            $em->remove($spend);
            $em->flush();
        }
        return $this->redirect()->toRoute('accantona_accantonato');
    }

    public function getAccantonatoTable()
    {
        if (!$this->accantonatoTable) {
            $this->accantonatoTable = $this->getServiceLocator()->get('Accantona\Model\AccantonatoTable');
        }
        return $this->accantonatoTable;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        }
        return $this->user;
    }

    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

}