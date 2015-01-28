<?php
namespace CkTools\Controller;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use CkTools\Controller\AppController;

/**
 * SystemContents Controller
 *
 * @property CkTools\Model\Table\SystemContentsTable $SystemContents
 */
class SystemContentsController extends AppController
{

    /**
     * beforeFilter event
     *
     * @param \Cake\Event\Event $event cake event
     * @return void
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        $config = [
            'layout' => 'default',
            'beforeFilter' => function ($controller, $config) {
            }
        ];
        if ($appConfig = Configure::read('CkTools.SystemContents')) {
            $config = Hash::merge($config, $appConfig);
        }
        $this->layout = $config['layout'];
        if (is_callable($config['beforeFilter'])) {
            $config['beforeFilter']($this, $config);
        }

        $this->loadModel('CkTools.SystemContents');
        $this->SystemContents->locale('eng'); # hard-coded for now
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('systemContents', $this->paginate($this->SystemContents));
    }

    /**
     * View method
     *
     * @param string $id Entity ID
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = null)
    {
        $systemContent = $this->SystemContents->get($id, [
            'contain' => []
        ]);
        $this->set('systemContent', $systemContent);
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $systemContent = $this->SystemContents->newEntity($this->request->data);
        if ($this->request->is('post')) {
            if ($this->SystemContents->save($systemContent)) {
                $this->Flash->success(__('crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('crud.validation_failed'));
            }
        }
        $this->set(compact('systemContent'));
        return $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string $id Entity ID
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $systemContent = $this->SystemContents->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $systemContent = $this->SystemContents->patchEntity($systemContent, $this->request->data);
            if ($this->SystemContents->save($systemContent)) {
                $this->Flash->success(__('crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('crud.validation_failed'));
            }
        }
        $this->set(compact('systemContent'));
        return $this->render('form');
    }

    /**
     * Delete method
     *
     * @param string $id Entity ID
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null)
    {
        $systemContent = $this->SystemContents->get($id);
        $this->request->allowMethod('post', 'delete');
        if ($this->SystemContents->delete($systemContent)) {
            $this->Flash->success('system_contents.delete_successful');
        } else {
            $this->Flash->error('crud.delete_failed');
        }
        return $this->redirect(['action' => 'index']);
    }
}
