<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $limit = 10;

    /*
     * breadcrumbs variable, format like
     * [['link 1', 'link title 1'], ['link 2', 'link title 2']]
     *
     * */
    public $breadcrumbs = [
        ['users', 'Pengguna']
    ];

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow('login', 'add');
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->set('isLoginError', true);
            $this->Flash->error(__('Invalid username or password, try again'));
        }
        //$this->viewBuilder()->theme('Bootstrap');
        $this->viewBuilder()->layout('login');
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Users->find('all', [
            'conditions' => ['Users.active' => 1],
            'contain' => [
                'Groups' => ['conditions' => ['Groups.active' => 1]],
                'Departements'
            ],
            'limit' => $this->limit
        ]);
        if ($this->request->query('search'))
        {
            $query->where(['LOWER(fullname) LIKE' => '%' . strtolower($this->request->query('search')) . '%']);
        }
        if ($this->request->query('sort'))
        {
            $query->order([
                $this->request->query('sort') => $this->request->query('direction')
            ]);
        } else {
            $query->order(['fullname' => 'ASC']);
        }
        $this->paginate = ['limit' => $this->limit];
        $breadcrumbs = $this->breadcrumbs;
        $this->set('breadcrumbs', $breadcrumbs);

        $users = $this->paginate($query);
        $this->set('title', 'Pengguna');
        $this->set('limit', $this->limit);
        $this->set('isShowAddButton', true);
        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Groups', 'Departements', 'Dispositions', 'Evidences', 'Letters']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $newUser = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $now = Time::now();
            $this->request->data['password'] = '1';
            $this->request->data['active'] = 1;
            $this->request->data['departements'] = [
                ['id' => $this->request->data['departement_id'],
                '_joinData' => [
                    'start' => $now->i18nFormat('yyyy-MM-dd'),
                    'end' => NULL,
                    'active' => 1]
                ]
            ];
            $newUser = $this->Users->newEntity($this->request->data, ['associated' => ['Departements._joinData']]);
            if ($this->Users->save($newUser)) {
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $groups = $this->Users->Groups->find('list', ['limit' => 200]);
        $departements = $this->Users->Departements->find('treeList');
        $this->set('title', 'Pengguna');
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'add/',
            'Tambah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set(compact('newUser', 'groups', 'departements'));
        $this->set('_serialize', ['newUser']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $editUser = $this->Users->get($id, [
            'contain' => ['Departements', 'Groups']
        ]);
        $oldDepartement = $this->Users->DepartementsUsers->find('all', [
            'conditions' => [
                'DepartementsUsers.user_id' => $id,
                'DepartementsUsers.active' => 1
            ],
            'order' => ['DepartementsUsers.start' => 'DESC']
        ]);
        if ($oldDepartement->count() > 0) {
            $departementId = $oldDepartement->first();
        } else {
            $departementId['departement_id'] = 0;
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $editUser = $this->Users->patchEntity($editUser, $this->request->data);
            if ($this->Users->save($editUser)) {
                if ($departementId['departement_id'] != $this->request->data['departement_id']) {
                    $this->updateDepartement($editUser['id'], $departementId['departement_id'], $this->request->data['departement_id']);
                }
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $groups = $this->Users->Groups->find('list', ['limit' => 200]);
        $departements = $this->Users->Departements->find('treeList');
        $this->set('title', 'Pengguna');
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'edit/',
            'Ubah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('departementId', $departementId);
        $this->set(compact('editUser', 'groups', 'departements'));
        $this->set('_serialize', ['editUser']);
    }

    private function updateDepartement($userId = null, $oldDepartement = null, $newDepartement = null) {
        $now = Time::now();
        $yesterday = $now->modify('-1 day');

        // if user have old departement
        if ($this->Users->DepartementsUsers->exists(['id' => $oldDepartement])) {
            $old = $this->Users->DepartementsUsers->get($oldDepartement);
            $old->end = $yesterday->i18nFormat('yyyy-MM-dd');
            $this->Users->DepartementsUsers->save($old);
        }

        $today = $now->modify('+1 day');
        $new = $this->Users->DepartementsUsers->newEntity();
        $data = [
            'departement_id' => $newDepartement,
            'user_id' => $userId,
            'start' => $now->i18nFormat('yyyy-MM-dd'),
            'active' => 1
        ];
        $new = $this->Users->DepartementsUsers->patchEntity($new, $data);
        $this->Users->DepartementsUsers->save($new);

        return true;
    }
    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $user = $this->Users->get($id);
        $user->active = 0;
        $this->Users->save($user);
        /*$this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }*/

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reset method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function reset($id = null)
    {
        $user = $this->Users->get($id);
        $user->password = '1';
        $this->Users->save($user);
        return $this->redirect(['action' => 'index']);
    }
}
