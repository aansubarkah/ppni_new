<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Letters Controller
 *
 * @property \App\Model\Table\LettersTable $Letters
 */
class LettersController extends AppController
{
    public $limit = 10;

    /*
     * breadcrumbs variable, format like
     * [['link 1', 'link title 1'], ['link 2', 'link title 2']]
     *
     * */
    public $breadcrumbs = [
        ['letters', 'Surat Masuk']
    ];

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Letters->find('all', [
            'contain' => ['Senders'],
            'limit' => $this->limit
        ]);
        if ($this->request->query('search'))
        {
            $query->where(['content LIKE' => '%' . $this->request->query('search') . '%']);
        }
        if ($this->request->query('sort'))
        {
            $query->order([
                $this->request->query('sort') => $this->request->query('direction')
            ]);
        } else {
            $query->order(['date' => 'DESC']);
        }
        $this->paginate = ['limit' => $this->limit];
        /*$this->paginate = [
            'contain' => ['Senders', 'Users', 'Vias'],
            'order' => ['date' => 'DESC'],
            'limit' => $this->limit
        ];*/
        $breadcrumbs = $this->breadcrumbs;
        $this->set('breadcrumbs', $breadcrumbs);

        $letters = $this->paginate($query);
        $this->set('title', 'Surat Masuk');
        $this->set('limit', $this->limit);
        $this->set('isShowAddButton', true);
        $this->set(compact('letters'));
        $this->set('_serialize', ['letters']);
    }

    /**
     * View method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $letter = $this->Letters->get($id, [
            'contain' => ['Senders', 'Users', 'Vias', 'Evidences', 'Dispositions']
        ]);

        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'view/' . $letter['id'],
            $letter['number']
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('title', $letter['number']);
        $this->set('letter', $letter);
        $this->set('_serialize', ['letter']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $letter = $this->Letters->newEntity();
        if ($this->request->is('post')) {
            $letter = $this->Letters->patchEntity($letter, $this->request->data);
            if ($this->Letters->save($letter)) {
                $this->Flash->success(__('The letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The letter could not be saved. Please, try again.'));
            }
        }
        $senders = $this->Letters->Senders->find('list', ['limit' => 200]);
        $users = $this->Letters->Users->find('list', ['limit' => 200]);
        $vias = $this->Letters->Vias->find('list', ['limit' => 200]);
        $evidences = $this->Letters->Evidences->find('list', ['limit' => 200]);
        $this->set(compact('letter', 'senders', 'users', 'vias', 'evidences'));
        $this->set('_serialize', ['letter']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $letter = $this->Letters->get($id, [
            'contain' => ['Evidences']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $letter = $this->Letters->patchEntity($letter, $this->request->data);
            if ($this->Letters->save($letter)) {
                $this->Flash->success(__('The letter has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The letter could not be saved. Please, try again.'));
            }
        }
        $senders = $this->Letters->Senders->find('list', ['limit' => 200]);
        $users = $this->Letters->Users->find('list', ['limit' => 200]);
        $vias = $this->Letters->Vias->find('list', ['limit' => 200]);
        $evidences = $this->Letters->Evidences->find('list', ['limit' => 200]);
        $this->set(compact('letter', 'senders', 'users', 'vias', 'evidences'));
        $this->set('_serialize', ['letter']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Letter id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $letter = $this->Letters->get($id);
        if ($this->Letters->delete($letter)) {
            $this->Flash->success(__('The letter has been deleted.'));
        } else {
            $this->Flash->error(__('The letter could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function isAuthorized($user)
    {
        // All registered users can add letters
        if ($this->request->action === 'add') {
            return true;
        }

        //
        return parent::isAuthorized($user);
    }

    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->viewBuilder()->theme('Bootstrap');
    }

}
