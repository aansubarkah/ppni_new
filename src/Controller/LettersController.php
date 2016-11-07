<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Date;

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

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('CakephpJqueryFileUpload.JqueryFileUpload');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Letters->find('all', [
            'conditions' => ['Letters.active' => 1],
            'contain' => ['Senders' => ['conditions' => ['Senders.active' => 1]]],
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
            'contain' => [
                'Senders',
                'Users',
                'Vias',
                'Evidences' => ['conditions' => ['Evidences.active' => 1]],
                'Dispositions']
        ]);

        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'view/' . $letter['id'],
            $letter['number']
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('isShowEditButton', true);

        $this->set('controllerObjectId', $id);

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
            // convert date to cakephp format
            $date = new Date($this->request->data['date']);
            $this->request->data['date'] = $date->format('Y-m-d');

            // remove files, used by blueimp upload plugin on template form
            unset($this->request->data['files']);

            // move evidence_id from request data
            $evidence_id = $this->request->data['evidence_id'];
            unset($this->request->data['evidence_id']);

            // check sender with id and name then unset sender name
            $sender_id = $this->getSenderId(
                $this->request->data['sender_name'],
                $this->request->data['sender_id']
            );
            $this->request->data['sender_id'] = $sender_id;
            unset($this->request->data['sender_name']);

            // set the unset variable
            $this->request->data['active'] = 1;
            $this->request->data['user_id'] = $this->Auth->user('id');
            $this->request->data['isread'] = 1;

            $letter = $this->Letters->patchEntity($letter, $this->request->data);
            if ($this->Letters->save($letter)) {
                if (intval($evidence_id) != 0) {
                    $evidence = $this->Letters->Evidences->findById($evidence_id)->first();
                    $letter->evidences = [$evidence];
                    if ($this->Letters->save($letter)) {
                        //return $this->redirect(['action' => 'index']);
                    }
                }
                return $this->redirect(['action' => 'index']);

                //$this->Flash->success(__('The letter has been saved.'));

                //return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The letter could not be saved. Please, try again.'));
            }
        }
        // convert senders to id -> name array
        $senders = $this->Letters->Senders->find('all', [
            'where' => ['active' => 1],
            'order' => ['name' => 'ASC']
        ]);
        $sendersOptions = [];
        foreach($senders as $sender)
        {
            $sendersOptions[$sender->id] = $sender->name;
        }
        $this->set('sendersOptions', $sendersOptions);

        // convert via to cakephp-form-select-options
        $vias = $this->Letters->Vias->find('all', [
            'where' => ['active' => 1],
            'order' => ['name' => 'ASC']
        ]);
        $viasOptions = [];
        foreach($vias as $via)
        {
            $viasOptions[$via->id] = $via->name;
        }
        $this->set('viasOptions', $viasOptions);

        $this->set(compact('letter', 'evidences'));
        $this->set('title', 'Surat Masuk');
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'add/',
            'Tambah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('_serialize', ['letter']);
    }

    /**
    *
    */
    private function getSenderId($name = null, $id = null)
    {
        $sender = $this->Letters->Senders->find('all', [
            'conditions' => [
                'Senders.id' => $id,
                'Senders.name' => $name,
                'Senders.active' => 1
            ]
        ]);

        if ($sender->count() != 1)
        {
            $newSender = $this->Letters->Senders->newEntity();
            $newSender->name = $name;
            $newSender->active = 1;
            if ($this->Letters->Senders->save($newSender)) {
                $sender_id = $newSender->id;
            }
        } else {
            $sender_id = $id;
        }
        return $sender_id;
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
            'contain' => [
                'Evidences' => ['conditions' => ['Evidences.active' => 1]],
                'Senders',
                'Vias'
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // convert date to cakephp format
            $date = new Date($this->request->data['date']);
            $this->request->data['date'] = $date->format('Y-m-d');

            // remove files, used by blueimp upload plugin on template form
            unset($this->request->data['files']);

            // move evidence_id from request data
            $evidence_id = $this->request->data['evidence_id'];
            unset($this->request->data['evidence_id']);

            // check sender with id and name then unset sender name
            $sender_id = $this->getSenderId(
                $this->request->data['sender_name'],
                $this->request->data['sender_id']
            );
            $this->request->data['sender_id'] = $sender_id;
            unset($this->request->data['sender_name']);
            unset($this->request->data['sender']);

            // set the unset variable
            $this->request->data['active'] = 1;
            $this->request->data['user_id'] = $this->Auth->user('id');
            $this->request->data['isread'] = 1;

            $letter->sender_id = $sender_id;
            $letter->user_id = $this->request->data['user_id'];
            $letter->via_id = $this->request->data['via_id'];
            $letter->number = $this->request->data['number'];
            $letter->date = $this->request->data['date'];
            $letter->content = $this->request->data['content'];
            $letter->isread = 1;
            $letter->active = 1;
            if ($this->Letters->save($letter)) {
                if (intval($evidence_id) != 0) {
                    $evidence = $this->Letters->Evidences->findById($evidence_id)->first();
                    $letter->evidences = [$evidence];
                    $this->Letters->save($letter);
                    //if ($this->Letters->save($letter)) {
                        //return $this->redirect(['action' => 'index']);
                    //}
                }
                return $this->redirect('/letters/view/' . $letter->id);
            } else {
                $this->Flash->error(__('The letter could not be saved. Please, try again.'));
            }
        }

        // convert date to datepicker format
        $date = new Date($letter->date);
        $letter->date = $date->format('d-m-Y');

        // convert senders to id -> name array
        $senders = $this->Letters->Senders->find('all', [
            'where' => ['active' => 1],
            'order' => ['name' => 'ASC']
        ]);
        $sendersOptions = [];
        foreach($senders as $sender)
        {
            $sendersOptions[$sender->id] = $sender->name;
        }
        $this->set('sendersOptions', $sendersOptions);

        // convert via to cakephp-form-select-options
        $vias = $this->Letters->Vias->find('all', [
            'where' => ['active' => 1],
            'order' => ['name' => 'ASC']
        ]);
        $viasOptions = [];
        foreach($vias as $via)
        {
            $viasOptions[$via->id] = $via->name;
        }
        $this->set('viasOptions', $viasOptions);

        $this->set(compact('letter', 'evidences'));
        $this->set('title', $letter->number);
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'letters/view/' . $letter->id,
            $letter->number
        ]);
        array_push($breadcrumbs, [
            'letters',
            'Ubah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('_serialize', ['letter']);

        //$senders = $this->Letters->Senders->find('list', ['limit' => 200]);
        //$users = $this->Letters->Users->find('list', ['limit' => 200]);
        //$vias = $this->Letters->Vias->find('list', ['limit' => 200]);
        //$evidences = $this->Letters->Evidences->find('list', ['limit' => 200]);
        //$this->set(compact('letter', 'senders', 'users', 'vias', 'evidences'));
        //$this->set('_serialize', ['letter']);
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
        //$this->request->allowMethod(['post', 'delete']);
        $letter = $this->Letters->get($id);
        $letter->active = 0;
        $this->Letters->save($letter);
        /*if ($this->Letters->delete($letter)) {
            $this->Flash->success(__('The letter has been deleted.'));
        } else {
            $this->Flash->error(__('The letter could not be deleted. Please, try again.'));
        }*/

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
