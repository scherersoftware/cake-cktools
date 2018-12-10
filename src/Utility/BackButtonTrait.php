<?php
declare(strict_types = 1);
namespace CkTools\Utility;

/**
 * This trait provides functionality for back buttons.
 * Usable wherever `$this->request` exists (Components, Helpers, Controllers)
 *
 * @property \Cake\Http\ServerRequest $request
 */
trait BackButtonTrait
{

    /**
     * Returns the requested action excluding the back action.
     *
     * @return string
     */
    public function getRequestedAction(): string
    {
        /*
         * Remove back_action from query string but keep the `?` if it is the first query param and there are additional query params following.
         */
        $requestedAction = preg_replace('/back_action=.*?(&|$)/', '', $this->request->getRequestTarget());

        /*
         * If `?` is the last char in the url we can remove it.
         */
        return preg_replace('/\\?$/', '', $requestedAction);
    }

    /**
     * persists requested back actions with their context in session
     *
     * @return void
     */
    public function handleBackActions(): void
    {
        if (!$this->request->getSession()->check('back_action')) {
            $this->request->getSession()->write('back_action', []);
        }
        if (!empty($this->request->getQuery('back_action'))) {
            $requestedBackAction = $this->request->getQuery('back_action');
            $requestedAction = $this->getRequestedAction();

            if (!$this->request->getSession()->check('back_action.' . $requestedBackAction)
                || ($this->request->getSession()->check('back_action.' . $requestedBackAction)
                    && $this->request->getSession()->read('back_action.' . $requestedBackAction) != $requestedAction
                )
                && !$this->request->getSession()->check('back_action.' . $requestedAction)
            ) {
                $this->request->getSession()->write('back_action.' . $requestedAction, $requestedBackAction);
            }
        }
    }

    /**
     * Adds a back action get param to an url array
     *
     * @param array $url URL array
     * @return array
     */
    public function augmentUrlByBackParam(array $url): array
    {
        $backAction = $this->request->getRequestTarget();
        if ($this->request->is('ajax')) {
            $backAction = $this->request->referer(true);
        }
        $backAction = preg_replace('/back_action=.*?(&|$)/', '', $backAction);

        $url['?']['back_action'] = preg_replace('/\\?$/', '', $backAction);

        return $url;
    }
}
