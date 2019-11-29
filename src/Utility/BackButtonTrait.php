<?php
declare(strict_types = 1);
namespace CkTools\Utility;

use Cake\Http\Exception\InternalErrorException;

/**
 * This trait provides functionality for back buttons for Controllers, Components, Helpers and so on
 * `$this->serverRequest` MUST be manually set to contain the request object.
 *
 * @property \Cake\Http\ServerRequest $serverRequest
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
        $requestedAction = preg_replace(
            '/back_action=.*?(&|$)/',
            '',
            $this->serverRequest->getRequestTarget()
        );

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
        if (empty($this->serverRequest)) {
            throw new InternalErrorException(
                '$serverRequest must contain the request object to use BackButtonTrait in ' . self::class
            );
        }

        $session = $this->serverRequest->getSession();
        if (!$session->check('back_action')) {
            $session->write('back_action', []);
        }
        if (!empty($this->serverRequest->getQuery('back_action'))) {
            $requestedBackAction = $this->serverRequest->getQuery('back_action');
            $requestedAction = $this->getRequestedAction();

            if (
                !$session->check('back_action.' . $requestedBackAction)
                || ($session->check('back_action.' . $requestedBackAction)
                    && $session->read('back_action.' . $requestedBackAction) != $requestedAction
                )
                && !$session->check('back_action.' . $requestedAction)
            ) {
                $session->write('back_action.' . $requestedAction, $requestedBackAction);
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
        $backAction = $this->serverRequest->getRequestTarget();
        if ($this->serverRequest->is('ajax')) {
            $backAction = $this->serverRequest->referer(true);
        }
        $backAction = preg_replace('/back_action=.*?(&|$)/', '', $backAction);

        $url['?']['back_action'] = preg_replace('/\\?$/', '', $backAction);

        return $url;
    }
}
