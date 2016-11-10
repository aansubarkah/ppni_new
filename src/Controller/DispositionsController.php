<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Dispositions Controller
 *
 * @property \App\Model\Table\DispositionsTable $Dispositions
 */
class DispositionsController extends AppController
{
    /*
     * breadcrumbs variable, format like
     * [['link 1', 'link title 1'], ['link 2', 'link title 2']]
     *
     * */
    public $breadcrumbs = [
        ['dispositions', 'Disposisi']
    ];

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['ParentDispositions', 'Letters', 'Users', 'Recipients']
        ];
        $dispositions = $this->paginate($this->Dispositions);

        $this->set(compact('dispositions'));
        $this->set('_serialize', ['dispositions']);
    }

    /**
     * View method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => ['ParentDispositions', 'Letters', 'Users', 'Recipients', 'Evidences', 'ChildDispositions']
        ]);

        $this->set('disposition', $disposition);
        $this->set('_serialize', ['disposition']);
    }

    /**
     * Add method
     *
     * @param string|null $id Letter id.
     * @param string|null $parent_id Sender parent_id.
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null, $parent_id = null)
    {
        if ($this->Dispositions->Letters->exists(['id' => $id, 'active' => 1]))
        {
            $letter = $this->Dispositions->Letters->get($id, [
                'contain' => [
                    'Evidences' => [
                        'conditions' => ['Evidences.active' => 1]
                    ],
                    'Senders'
                ]
            ]);
            $disposition = $this->Dispositions->newEntity();
            if ($this->request->is('post')) {
                intval($this->request->data['parent_id']) == 0 ? $parent_id = NULL : $parent_id = $this->request->data['parent_id'];
                $evidence_id = intval($this->request->data['evidence_id']);

                // first get how many recipients
                $recipients = explode(",", $this->request->data['recipients']);
                foreach ($recipients as $recipient) {
                    $newDisposition = $disposition;
                    // get user on recipient departement
                    $usersOnRecipient = $this->Dispositions->Users->find('all', [
                        'conditions' => ['Users.active' => 1]
                    ])
                    ->matching('Departements', function($q) use ($recipient) {
                        return $q->where(['Departements.id' => $recipient]);
                    });

                    // foreach user
                    foreach ($usersOnRecipient as $user) {
                        $newDisposition = $this->Dispositions->newEntity();
                        $newDisposition->parent_id = $parent_id;
                        $newDisposition->letter_id = intval($this->request->data['letter_id']);
                        $newDisposition->user_id = $this->Auth->user('id');
                        $newDisposition->recipient_id = $user['id'];
                        $newDisposition->content = $this->request->data['content'];
                        $newDisposition->isread = 0;
                        $newDisposition->finish = 0;
                        $newDisposition->active = 1;
                        if ($this->Dispositions->save($newDisposition)) {
                            if ($evidence_id > 0) {
                                $evidence = $this->Dispositions->Evidences->findById($evidence_id)->first();
                                $newDisposition->evidences = [$evidence];
                                $this->Dispositions->save($newDisposition);
                            }
                        }
                    }
                }

                // if saving success
                return $this->redirect('/letters/view/' . $this->request->data['letter_id']);
            }

            // only display departements with users exists and not active user departement
            // first get current user's departement
            $userDepartement = $this->Dispositions->Users->find('all', [
                'conditions' => ['Users.id' => $this->Auth->user('id')],
                'contain' => ['Departements']
            ])->first();
            // if user have departement use it, if not, use departement 1 which is PPNI Jatim
            count($userDepartement['departements']) > 0 ? $departement_to_avoid = $userDepartement['departements'][0]['id'] : $departement_to_avoid = 1;
            //print_r($userDepartement['departements'][0]['id']);
            $departements = $this->Dispositions->Users->Departements
                ->find('all', [
                    'conditions' => [
                        'Departements.active' => 1,
                        'Departements.parent_id !=' => 0,
                        'Departements.id !=' => $departement_to_avoid
                    ],
                    'order' => ['name' => 'ASC']
                ])
                ->matching('Users', function($q) {
                    return $q->where(['Users.active' => 1]);
                });
            $departementsOptions = [];
            foreach ($departements as $departement) {
                $departementsOptions[$departement->id] = $departement->name;
            }

            $this->set('title', 'Disposisi');
            $breadcrumbs = $this->breadcrumbs;
            array_push($breadcrumbs, [
                'dispositions/add',
                'Tambah'
            ]);

            $this->set('breadcrumbs', $breadcrumbs);

            $this->set('letter_id', $id);

            empty($parent_id) ? $parent = 0 : $parent = $parent_id;
            $this->set('parent_id', $parent);

            // get parent disposition, if exists
            if ($parent_id > 0) {
                $parent = $this->Dispositions->get($parent_id,
                    ['contain' => ['Evidences', 'Users']]);

                $parentDepartement = $this->Dispositions->Users->find('all', [
                    'conditions' => ['Users.id' => $parent['user_id']],
                    'contain' => ['Departements']
                ])->first();

                $this->set('parent', $parent);
                $this->set('parentDepartement', $parentDepartement['departements']);
            }

            $this->set(compact('disposition', 'departementsOptions', 'letter'));
            $this->set('_serialize', ['disposition', 'departementsOptions', 'letter']);
        } else {
            $this->redirect(['controller' => 'letters', 'action' => 'index']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $disposition = $this->Dispositions->get($id, [
            'contain' => ['Evidences']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $disposition = $this->Dispositions->patchEntity($disposition, $this->request->data);
            if ($this->Dispositions->save($disposition)) {
                $this->Flash->success(__('The disposition has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The disposition could not be saved. Please, try again.'));
            }
        }

        $letter = $this->Dispositions->Letters->get($disposition['letter_id'], [
            'contain' => ['Evidences', 'Senders']
        ]);
        // only display departements with users exists and not active user departement
        // first get current user's departement
        $userDepartement = $this->Dispositions->Users->find('all', [
            'conditions' => ['Users.id' => $this->Auth->user('id')],
            'contain' => ['Departements']
        ])->first();
        // if user have departement use it, if not, use departement 1 which is PPNI Jatim
        count($userDepartement['departements']) > 0 ? $departement_to_avoid = $userDepartement['departements'][0]['id'] : $departement_to_avoid = 1;
        //print_r($userDepartement['departements'][0]['id']);
        $departements = $this->Dispositions->Users->Departements
            ->find('all', [
                'conditions' => [
                    'Departements.active' => 1,
                    'Departements.parent_id !=' => 0,
                    'Departements.id !=' => $departement_to_avoid
                ],
                'order' => ['name' => 'ASC']
            ])
            ->matching('Users', function($q) {
                return $q->where(['Users.active' => 1]);
            });
        $departementsOptions = [];
        foreach ($departements as $departement) {
            $departementsOptions[$departement->id] = $departement->name;
        }

        $this->set('title', 'Disposisi Surat Masuk ' . $letter['number']);
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'letters/view/' . $letter['id'],
            $letter['number']
        ]);
        array_push($breadcrumbs, [
            'dispositions/edit',
            'Ubah'
        ]);
        $this->set('breadcrumbs', $breadcrumbs);

        $this->set(compact('disposition', 'departementsOptions', 'letter'));
        $this->set('_serialize', ['disposition', 'departementsOptions', 'letter']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Disposition id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $disposition = $this->Dispositions->get($id);
        $disposition->active = 0;
        $this->Dispositions->save($disposition);

        return $this->redirect([
            'controller' => 'letters',
            'action' => 'view',
            $disposition['letter_id']
        ]);

        /*$this->request->allowMethod(['post', 'delete']);
        $disposition = $this->Dispositions->get($id);
        if ($this->Dispositions->delete($disposition)) {
            $this->Flash->success(__('The disposition has been deleted.'));
        } else {
            $this->Flash->error(__('The disposition could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);*/
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
