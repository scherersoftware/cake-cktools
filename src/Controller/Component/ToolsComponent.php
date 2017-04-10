<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use CkTools\Utility\BackButtonTrait;

/**
 * Tools Component
 */
class ToolsComponent extends Component
{
    use BackButtonTrait;

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        $this->handleBackActions();
    }

}
