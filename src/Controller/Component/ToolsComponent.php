<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Tools Component
 */
class ToolsComponent extends Component
{

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        $this->_handleBackActions();
    }

    /**
     * persists requested back actions with their context in session
     *
     * @return void
     */
    protected function _handleBackActions()
    {
        if (!$this->request->session()->check('back_action')) {
            $this->request->session()->write('back_action', []);
        }
        if (!empty($this->request->query['back_action'])) {
            $requestedBackAction = $this->request->query['back_action'];
            $requestedAction = preg_replace('/(\\?|&)back_action=.*?(&|$)/', '', $this->request->here(false));

            if (!$this->request->session()->check('back_action.' . $requestedBackAction)
                || ($this->request->session()->check('back_action.' . $requestedBackAction) 
                    && $this->request->session()->read('back_action.' . $requestedBackAction) != $requestedAction
                )
                && (!$this->request->session()->check('back_action.' . $requestedAction))
            ) {
                $this->request->session()->write('back_action.' . $requestedAction, $requestedBackAction);
            }
        }
    }
}
