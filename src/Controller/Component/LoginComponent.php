<?php

namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use CkTools\Utility\AutoLogin;

class LoginComponent extends Component
{
    protected $_defaultConfig = [
        'defaultRedirect' => '/'
    ];

    /**
     * Attempts to auto login a user and returns a redirect on success.
     *
     * @param \Cake\Datasource\EntityInterface $user User
     * @param string                           $key  Seurity key (should be user specific)
     * @param string                           $salt Security salt (should be user specific)
     * @return \Cake\Http\Response|null
     */
    public function autoLogin(EntityInterface $user, string $key, string $salt): ?Response
    {
        $controller = $this->getController();
        $request = $controller->request;
        $token = $request->getQuery('t');

        if (empty($token)) {
            return null;
        }

        $controller->Auth->logout();

        $tokenData = AutoLogin::validateLoginToken($token, $key, $salt);

        if (!is_array($tokenData)) {
            return null;
        }

        if (!empty($tokenData['addRememberMeCookie']) && $tokenData['addRememberMeCookie']) {
            $controller->AuthUtils->addRememberMeCookie($user->id);
        }

        $userData = $user->toArray();
        $userData['user'] = $user;
        $controller->Auth->setUser($userData);

        if (!empty($tokenData['url'])) {
            return $controller->redirect($tokenData['url']);
        }

        return $controller->redirect($this->getConfig('defaultRedirect'));
    }

}
