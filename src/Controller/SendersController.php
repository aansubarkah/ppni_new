<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Senders Controller
 *
 * @property \App\Model\Table\SendersTable $Senders
 */
class SendersController extends AppController
{
    public $limit = 5;

    /*
     * breadcrumbs variable, format like
     * [['link 1', 'link title 1'], ['link 2', 'link title 2']]
     *
     * */
    public $breadcrumbs = [
        ['senders', 'Pengirim']
    ];


    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Senders->find('all', [
            'conditions' => ['Senders.active' => 1],
            'limit' => $this->limit
        ]);
        if ($this->request->query('search'))
        {
            $query->where(['name LIKE' => '%' . $this->request->query('search') . '%']);
        }
        if ($this->request->query('sort'))
        {
            $query->order([
                $this->request->query('sort') => $this->request->query('direction')
            ]);
        } else {
            $query->order(['name' => 'ASC']);
        }
        $this->paginate = ['limit' => $this->limit];

        $breadcrumbs = $this->breadcrumbs;
        $this->set('breadcrumbs', $breadcrumbs);

        $senders = $this->paginate($query);
        $this->set('title', 'Pengirim');
        $this->set('limit', $this->limit);
        //$this->set('isShowAddButton', true);
        $this->set(compact('senders'));
        $this->set('_serialize', ['senders']);
    }

    /**
     * View method
     *
     * @param string|null $id Sender id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sender = $this->Senders->get($id);

        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'view/' . $sender['id'],
            $sender['name']
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('isShowEditButton', true);

        $this->set('controllerObjectId', $id);

        $this->set('title', $sender['name']);
        $this->set('sender', $sender);
        $this->set('_serialize', ['sender']);

        $query = $this->Senders->Letters->find('all', [
            'conditions' => ['Letters.active' => 1, 'Letters.sender_id' => $id],
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

        $letters = $this->paginate($query);
        $this->set('limit', $this->limit);
        $this->set(compact('letters'));
        $this->set('_serialize', ['letters']);

    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sender = $this->Senders->newEntity();
        if ($this->request->is('post')) {
            $sender = $this->Senders->patchEntity($sender, $this->request->data);
            if ($this->Senders->save($sender)) {
                $this->Flash->success(__('The sender has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The sender could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('sender'));
        $this->set('_serialize', ['sender']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Sender id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sender = $this->Senders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sender = $this->Senders->patchEntity($sender, $this->request->data);
            if ($this->Senders->save($sender)) {
                $this->Flash->success(__('The sender has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The sender could not be saved. Please, try again.'));
            }
        }
        $this->set('title', $sender->name);
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'senders/view/' . $sender->id,
            $sender->name
        ]);
        array_push($breadcrumbs, [
            'senders',
            'Ubah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set(compact('sender'));
        $this->set('_serialize', ['sender']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Sender id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //$this->request->allowMethod(['post', 'delete']);
        $sender = $this->Senders->get($id);
        $sender->active = 0;
        $this->Senders->save($sender);
        /*if ($this->Senders->delete($sender)) {
            $this->Flash->success(__('The sender has been deleted.'));
        } else {
            $this->Flash->error(__('The sender could not be deleted. Please, try again.'));
        }*/

        return $this->redirect(['action' => 'index']);
    }

    public function isAuthorized($user)
    {
        // All registered users can add, edit, delete
        if ($this->request->action === 'add' ||
            $this->request->action === 'edit' ||
            $this->request->action === 'delete') {
            return true;
        }

        //
        return parent::isAuthorized($user);
    }
}
