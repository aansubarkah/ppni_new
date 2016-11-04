<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Evidences Controller
 *
 * @property \App\Model\Table\EvidencesTable $Evidences
 */
class EvidencesController extends AppController
{

    private $filesDirectory = WWW_ROOT . 'files' . DS . 'evidences' . DS;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('CakephpJqueryFileUpload.JqueryFileUpload');
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $evidences = $this->paginate($this->Evidences);

        $this->set(compact('evidences'));
        $this->set('_serialize', ['evidences']);
    }

    /**
     * View method
     *
     * @param string|null $id Evidence id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $evidence = $this->Evidences->get($id, [
            'contain' => ['Users', 'Dispositions', 'Letters']
        ]);

        $this->set('evidence', $evidence);
        $this->set('_serialize', ['evidence']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    private function add($filename = NULL, $name = NULL)
    //public function add($filename = NULL, $name = NULL)
    {
        // setup a file object, in cakephp way
        $file = new File($this->filesDirectory . $filename, false, 755);

        // only if file exists
        if ($file->exists())
        {
            // get file extension
            $extension = $file->ext();
            $extension = strtolower($extension);
            empty($extension) ? $extension = 'pdf' : $extension = $extension;

            // get user_id
            $user_id = $this->Auth->user('id');

            // get name
            empty($name) ? $name = 'Berkas' : $name = $name;

            $data = [
                'user_id' => $user_id,
                'name' => $name,
                'extension' => $extension,
                'active' => 1
            ];

            // save to db
            $evidence = $this->Evidences->newEntity();
            $evidence = $this->Evidences->patchEntity($evidence, $data);
            if ($this->Evidences->save($evidence))
            {
                // rename file
                $oldName = $this->filesDirectory . $filename;
                $newName = $this->filesDirectory . $evidence->id . '.' . $extension;
                rename($oldName, $newName);
                //return true;
                return $evidence->id;
            } else {
                return false;
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Evidence id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $evidence = $this->Evidences->get($id, [
            'contain' => ['Dispositions', 'Letters']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $evidence = $this->Evidences->patchEntity($evidence, $this->request->data);
            if ($this->Evidences->save($evidence)) {
                $this->Flash->success(__('The evidence has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The evidence could not be saved. Please, try again.'));
            }
        }
        $users = $this->Evidences->Users->find('list', ['limit' => 200]);
        $dispositions = $this->Evidences->Dispositions->find('list', ['limit' => 200]);
        $letters = $this->Evidences->Letters->find('list', ['limit' => 200]);
        $this->set(compact('evidence', 'users', 'dispositions', 'letters'));
        $this->set('_serialize', ['evidence']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Evidence id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $evidence = $this->Evidences->get($id);
        if ($this->Evidences->delete($evidence)) {
            $this->Flash->success(__('The evidence has been deleted.'));
        } else {
            $this->Flash->error(__('The evidence could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function upload()
    {
        // example options
        // 'access_control_allow_origin' => Router::fullBaseUrl(),
        // 'max_number_of_files' => 1,
        // 'max_file_size' => 2048000,

        $options = array(
            'access_control_allow_methods' => ['POST'],
            'accept_file_types' => '/\.(jpe?g|png|tiff|pdf|docx?|pptx?|xlsx?|zip|rar|txt)$/i',
            'upload_dir' => $this->filesDirectory,
            'upload_url' => '/files/evidences/',
            'print_response' => false
        );

        $result = $this->JqueryFileUpload->upload($options);
        $evidence_id = $this->add($result['files'][0]->name, 'Surat Masuk');
        $evidence = ['id' => $evidence_id];

        $this->set([
            'result' => $result,
            'evidence' => $evidence,
            '_serialize' => ['result', 'evidence']
        ]);
    }

    public function download($id = null)
    {
        $isRecordExists = $this->Evidences->exists(['id' => $id, 'active' => 1]);
        if ($isRecordExists) {
            $evidence = $this->Evidences->find('all', [
                'conditions' => ['id' => $id, 'active' => 1]
            ]);
            $file = $evidence->first();

            $path = $this->filesDirectory . $id . '.' . $file['extension'];

            // create a cakephp file object
            $existingFile = new File($path, false, 755);

            // if exists on directory
            if ($existingFile->exists()) {
                $this->response->file(
                    $path,
                    ['download' => true, 'name' => str_replace(' ', '_', $file['name']) . '.' . $file['extension']]
                );
                return $this->response;
            }
        }
    }
}
