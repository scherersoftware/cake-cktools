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
            $requestedAction = $this->_getRequestedAction();

            if (!$this->request->session()->check('back_action.' . $requestedBackAction)
                || ($this->request->session()->check('back_action.' . $requestedBackAction)
                    && $this->request->session()->read('back_action.' . $requestedBackAction) != $requestedAction
                )
                && !$this->request->session()->check('back_action.' . $requestedAction)
            ) {
                $this->request->session()->write('back_action.' . $requestedAction, $requestedBackAction);
            }
        }
    }

    /**
     * Returns the requested action excluding the back action.
     *
     * @return string
     */
    protected function _getRequestedAction()
    {
        /*
         * Remove back_action from query string but keep the `?` if it is the first query param and there are additional query params following.
         */
        $requestedAction = preg_replace('/back_action=.*?(&|$)/', '', $this->request->here(false));

        /*
         * If `?` is the last char in the url we can remove it.
         */
        return preg_replace('/\\?$/', '', $requestedAction);
    }
}
