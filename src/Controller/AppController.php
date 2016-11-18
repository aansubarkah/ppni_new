<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'loginRedirect' => [
                'controller' => 'Letters',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Letters',
                'action' => 'index'
            ]
        ]);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
        $this->viewBuilder()->theme('Bootstrap');

        //$this->set('user', $this->Auth->user());
    }

    public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['index', 'view', 'display']);
        $this->request->is('mobile') ? $isMobile = true : $isMobile = false;
        $this->set('isMobile', $isMobile);
        $notifications = $this->getNotification();
        $this->set('notifications', $notifications);
    }

    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['group_id']) && $user['group_id'] == 1) {
            return true;
        }

        // Manager can access every action
        if (isset($user['group_id']) && $user['group_id'] == 2) {
            return true;
        }

        // Default deny
        return false;
    }

    public function getNotification()
    {
        $User = $this->loadModel('Users');
        $Letters = $this->loadModel('Letters');
        $Dispositions = $this->loadModel('Dispositions');
        $user = [];
        $letters = [];
        $dispositions = [];
        $lettersNumber = 0;
        $dispositionsNumber = 0;
        if ($this->Auth->user()) {
            $user = $User->get($this->Auth->user('id'), [
                'contain' => [
                    'Departements' => function ($q) {
                        return $q
                            ->where(['Departements.active' => 1]);
                    }
                ],
                //'conditions' => ['Departements.active' => 1]
            ]);

            // if Ketua or Sekretaris
            if ($user['departements'][0]['id'] == 2 ||
                $user['departements'][0]['id'] == 7 ||
                $user['departements'][0]['id'] == 8 ||
                $user['departements'][0]['id'] == 9) {
                $letters = $Letters->find('all', [
                    'conditions' => ['Letters.active' => 1, 'Letters.isread' => 0],
                    'contain' => ['Senders'],
                    'limit' => 3,
                    'order' => ['Letters.date' => 'DESC']
                ]);
                $lettersNumber = $letters->count();
            }
            $dispositions = $Dispositions->find('all', [
                'conditions' => ['Dispositions.active' => 1, 'Dispositions.isread' => 0, 'Dispositions.recipient_id' => $user['id']],
                'limit' => 3,
                'order' => ['Dispositions.created' => 'DESC'],
            ]);
            $dispositionsNumber = $dispositions->count();
        }
        $return = [
            'letters' => $letters,
            'dispositions' => $dispositions,
            'dispositionsNumber' => $dispositionsNumber,
            'lettersNumber' => $lettersNumber];
        return $return;
    }
}
