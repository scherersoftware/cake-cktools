<?php

namespace CkTools\Utility;

use Cake\I18n\Time;
use Cake\Routing\Router;
use Cake\Utility\Security;

class AutoLogin
{
    /**
     * Returns a full auto login url with token.
     *
     * @param string     $key                 Security key (Should be user specific)
     * @param string     $salt                Security salt (Should be user specific)
     * @param array      $autoUrl             URL configuration pointing to auto login page
     * @param array|null $redirectUrl         Optional redirect url
     * @param string     $expireInterval      When this token expires
     * @param bool       $addRememberMeCookie Enabling setting the remember me cookie on auto login
     * @return string
     * @throws \Exception
     */
    public static function getAutoLoginUrl(
        string $key,
        string $salt,
        array $autoUrl,
        array $redirectUrl = null,
        string $expireInterval = '1 day',
        bool $addRememberMeCookie = true
    ): string {
        $autoUrl['?']['t'] = self::generateLoginToken(
            $key,
            $salt,
            $redirectUrl,
            $expireInterval,
            $addRememberMeCookie
        );

        $url = Router::url($autoUrl, true);

        $urlLength = strlen($url);

        if (strlen($url) > 2080) {
            throw new \Exception('Generated url "' . $url . '" is too long');
        }

        return $url;
    }

    /**
     * Validates the token.
     *
     * @param string $token Token
     * @param string $key   Security key
     * @param string $salt  Security salt
     * @return array|null
     */
    public static function validateLoginToken(string $token, string $key, string $salt): ?array
    {
        $token = base64_decode($token);

        $serializedData = Security::decrypt($token, $key, $salt);

        if ($serializedData === false) {
            return null;
        }

        $data = unserialize($serializedData);

        if (!empty($data['expireInterval']) && !empty($data['timestamp'])) {
            $tokenCreated = new Time($data['timestamp']);
            if (!$tokenCreated->wasWithinLast($data['expireInterval'])) {
                return null;
            }
        }

        return $data;
    }

    /**
     * Generates a auto login token.
     *
     * @param string     $key                 Security key (Should be user specific)
     * @param string     $salt                Security salt (Should be user specific)
     * @param array|null $redirectUrl         Optional redirect url
     * @param string     $expireInterval      When this token expires
     * @param bool       $addRememberMeCookie Enabling setting the remember me cookie on auto login
     * @return string
     */
    public static function generateLoginToken(
        string $key,
        string $salt,
        array $redirectUrl = null,
        string $expireInterval = '1 day',
        bool $addRememberMeCookie = true
    ): string {
        $data = [
            'url' => $redirectUrl,
            'timestamp' => Time::now()->toUnixString(),
            'expireInterval' => $expireInterval,
            'addRememberMeCookie' => $addRememberMeCookie
        ];

        $serializedData = serialize($data);
        $token = Security::encrypt($serializedData, $key, $salt);

        return base64_encode($token);
    }
}
