<?php
namespace Accantona\Controller;

use Accantona\Model\AccantonatoTable;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Accantonato;
use Accantona\Form\AccantonatoForm;

class AccantonatoController extends AbstractActionController
{

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var AccantonatoTable
     */
    private $accantonatoTable;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(AccantonatoTable $accantonatoTable, \stdClass $user, EntityManager $em)
    {
        $this->accantonatoTable = $accantonatoTable;
        $this->user             = $user;
        $this->em               = $em;
    }

    public function indexAction()
    {
        $form = new AccantonatoForm();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $accantonato = new Accantonato();
            $form->setInputFilter($accantonato->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->user->id;
                $accantonato->exchangeArray($data);
                $this->accantonatoTable->save($accantonato);
                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }

        $where = array('userId=' . $this->user->id);
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'valuta>"' . date('Y-m-d', strtotime("-$months month")) . '"';
        }

        return new ViewModel(array(
            'months' => $months,
            'rows' => $this->accantonatoTable->fetchAll($where),
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $spend = $this->em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

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
                $this->em->flush();
                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }
        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $spend = $this->em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

        if ($spend) {
            $this->em->remove($spend);
            $this->em->flush();
        }
        return $this->redirect()->toRoute('accantona_accantonato');
    }
}