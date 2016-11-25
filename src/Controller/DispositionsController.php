<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Date;
use Cake\Mailer\Email;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

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
    public $limit = 10;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $query = $this->Dispositions->find('all', [
            'conditions' => ['Dispositions.active' => 1],
            'contain' => [
                'Letters' => ['conditions' => ['Letters.active' => 1]],
                'Recipients',
                'Users'
            ],
            'limit' => $this->limit
        ]);
        if ($this->request->query('search'))
        {
            $query->where(['LOWER(Dispositions.content) LIKE' => '%' . strtolower($this->request->query('search')) . '%']);
        }
        if ($this->request->query('sort'))
        {
            $query->order([
                'Dispositions.created' => $this->request->query('direction')
            ]);
        } else {
            $query->order(['Dispositions.created' => 'DESC']);
        }
        $this->paginate = ['limit' => $this->limit];

        $breadcrumbs = $this->breadcrumbs;
        $this->set('breadcrumbs', $breadcrumbs);

        $dispositions = $this->paginate($query);
        //print_r($dispositions);
        $this->set('title', 'Disposisi');
        $this->set('limit', $this->limit);
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
            'contain' => [
                'ParentDispositions',
                'Users',
                'Recipients',
                'Evidences',
                'ChildDispositions']
        ]);

        $letter = $this->Dispositions->Letters->get($disposition['letter_id'], [
            'contain' => [
                'Evidences',
                'Users',
                'Senders',
                'Vias'
            ]
        ]);

        $this->set('title', 'Disposisi');
        $breadcrumbs = $this->breadcrumbs;
        array_push($breadcrumbs, [
            'dispositions/add',
            'Rinci'
        ]);

        $this->set('breadcrumbs', $breadcrumbs);

        $this->set('disposition', $disposition);
        $this->set('letter', $letter);
        $this->set('_serialize', ['disposition', 'letter']);
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
                            if ($this->sendEmail($newDisposition->id)) {}
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
        if ($this->Auth->user('id') == $disposition['user_id']) {
            if ($this->request->is(['patch', 'post', 'put'])) {
                //print_r($this->request->data);
                unset($this->request->data['recipients']);
                unset($this->request->data['files']);
                $disposition = $this->Dispositions->patchEntity($disposition, $this->request->data);
                if ($this->Dispositions->save($disposition)) {
                    if ($this->request->data['evidence_id'] > 0) {
                        $evidence = $this->Dispositions->Evidences->findById($this->request->data['evidence_id'])->first();
                        $disposition->evidences = [$evidence];
                        $this->Dispositions->save($disposition);
                    }
                    return $this->redirect([
                        'controller' => 'letters',
                        'action' => 'view', $disposition->letter_id]);
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

            // get last recipients's departements
            $recipientDepartement = $this->Dispositions->Users->find('all', [
                'conditions' => ['Users.id' => $disposition['recipient_id']],
                'contain' => ['Departements']
            ])->first();
            $this->set('recipientDepartement', $recipientDepartement['departements']);

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
        } else {
            return $this->redirect([
                'controller' => 'letter',
                'action' => 'view',
                $disposition['letter_id']
            ]);
        }
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

    public function sendEmail($dispositionId) {
        // get letter info
        $disposition = $this->Dispositions->get($dispositionId, [
            'contain' => ['Users', 'Recipients', 'Letters']
        ]);
        $letter = $this->Dispositions->Letters->get($disposition['letter_id'], [
            'contain' => ['Senders']
        ]);

        if (!empty($disposition['recipient']['email'])) {
            Email::configTransport('gmail', [
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => 'serverppnijatim01@gmail.com',
                'password' => 'RukoG4teway',
                'className' => 'Smtp',
                'tls' => true
            ]);

            $email = new Email();
            $email->transport('gmail');
            $email->viewVars(['title' => 'Disposisi', 'disposition' => $disposition, 'letter' => $letter]);
            $email->template('disposition', 'default')
                ->helpers(['Url', 'Time'])
                ->emailFormat('html')
                ->from(['serverppnijatim01@gmail.com' => 'Surat Masuk PPNI Jatim'])
                ->to($disposition['recipient']['email'])
                ->subject('Disposisi')
                ->send();
        }
    }

    public function download($id = null)
    {
        $letter = $this->Dispositions->Letters->get($id, [
            'contain' => ['Senders']
        ]);
        $date = new Date($letter['date']);
        $newName = 'FormDisposisi_SuratMasuk_' . $letter['number'];
        $newName = $newName . '_Tanggal_' . $date->format('d_m_Y');
        $newName = $newName . '_Dari_' . $letter['sender']['name'] . '.pdf';

        $path = WWW_ROOT . 'files' . DS . 'dispositions' . DS . $id . '.pdf';
        // create a cakephp file object
        $existingFile = new File($path, false, 755);

        // if exists on directory
        if ($existingFile->exists()) {
            $this->response->file(
                $path,
                ['download' => true, 'name' => $newName]
            );
            return $this->response;
        }
    }
}
