<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Facebook\Entities\AccessToken;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;

/**
 * Facebook component
 */
class FacebookComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Session class
     *
     * @var \Cake\Network\Session
     */
    protected $_session;

    /**
     * Constructor
     *
     * @param ComponentRegistry $registry A ComponentRegistry object.
     * @param array             $config   Array of configuration settings.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        if ($registry->getController() instanceof \Cake\Controller\Controller) {
            $this->_session = $registry->getController()->request->session();
        }

        $facebookConfig = Configure::read('Facebook');
        FacebookSession::setDefaultApplication($facebookConfig['app_id'], $facebookConfig['app_secret']);
    }

    /**
     * Get a new instance of FacebookRedirectLoginHelper with optional redirectUrl
     *
     * @param  string                               $redirectUrl destination to be redirect to after calling the login URL
     * @return Facebook\FacebookRedirectLoginHelper new instance
     */
    protected function _getFacebookRedirectLoginHelper($redirectUrl = null)
    {
        if ($redirectUrl == null) {
            $redirectUrl = Router::url([
                'controller' => 'Login',
                'action' => 'facebook_login',
                'plugin' => null,
            ], true);
        }

        return new FacebookRedirectLoginHelper($redirectUrl);
    }

    /**
     * gets user data from facebook redirect
     *
     * @return array sanitized facebook user data
     */
    public function getUserFromRedirect()
    {
        $redirectUrl = $this->_session->read('Facebook.redirectUrl');
        $session = $this->_getSessionFromRedirect($redirectUrl);
        if ($session) {
            $userProfile = $this->getFullProfile($session);

            return $this->sanitizeResponse($userProfile);
        }
    }

    /**
     * gets a facebook session for a redirecting request
     *
     * @param  string                   $redirectUrl destination to be redirect to after calling the login URL
     * @return Facebook\FacebookSession session
     */
    protected function _getSessionFromRedirect($redirectUrl = null)
    {
        $facebookRedirectLoginHelper = $this->_getFacebookRedirectLoginHelper($redirectUrl);

        return $facebookRedirectLoginHelper->getSessionFromRedirect($redirectUrl);
    }

    /**
     * gets user data from facebook by using an access token
     *
     * @param  string $accessToken facebook access token
     * @return array  sanitized facebook user data
     */
    public function getUserFromAccessToken($accessToken = null)
    {
        $session = $this->_getSessionFromAccessToken($accessToken);
        if ($session) {
            $userProfile = $this->getFullProfile($session);

            return $this->sanitizeResponse($userProfile);
        }
    }

    /**
     * gets a facebook session for a redirecting request
     *
     * @param  string                   $accessToken facebook access token
     * @return Facebook\FacebookSession session
     */
    protected function _getSessionFromAccessToken($accessToken = null)
    {
        return new FacebookSession($accessToken);
    }

    /**
     * sanitizes the facebook user response data
     *
     * @param  array $fbResponse response of facebook api with user related data
     * @return array
     */
    public function sanitizeResponse($fbResponse)
    {
        $keyMap = [
            'facebook_id' => 'id',
            'email' => 'email',
            'firstname' => 'first_name',
            'lastname' => 'last_name',
            'gender' => 'gender',
            'date_of_birth' => 'birthday',
            'avatar' => 'avatarUrl',
        ];

        foreach ($keyMap as $newKey => $oldKey) {
            if (!empty($fbResponse[$oldKey])) {
                $userData[$newKey] = $fbResponse[$oldKey];
            } else {
                $userData[$newKey] = null;
            }
        }

        if ($userData['gender'] == 'male') {
            $userData['gender'] = 'm';
        } elseif ($userData['gender'] == 'female') {
            $userData['gender'] = 'f';
        } else {
            $userData['gender'] = null;
        }

        if ($userData['date_of_birth'] !== null) {
            list($month, $day, $year) = explode('/', $userData['date_of_birth']);
            $userData['date_of_birth'] = "{$year}-{$month}-{$day}";
        }

        if ($userData['facebook_id'] !== null) {
            $userData['facebook_id'] = intval($userData['facebook_id']);
        }

        return $userData;
    }

    /**
     * provides facebook login URL
     * used by webapp
     *
     * @param  string $redirectUrl destination to be redirect to after calling the login URL
     * @return string facebook login url
     */
    public function getLoginUrl($redirectUrl = null)
    {
        $this->_session->write('Facebook.redirectUrl', $redirectUrl);
        $facebookRedirectLoginHelper = $this->_getFacebookRedirectLoginHelper($redirectUrl);

        return $facebookRedirectLoginHelper->getLoginUrl(
            ['email', 'user_birthday']
        );
    }

    /**
     * get profile picture from facebook
     *
     * @param  Facebook\FacebookSession $session facebook session
     * @return string                   url to the profile picture
     */
    public function getProfilePicture($session)
    {
        $url = '/me/picture?type=large&width=1000&redirect=false';
        $fbRequest = new FacebookRequest($session, 'GET', $url);
        $profilePictureUrl = $fbRequest->execute()->getGraphObject()->asArray();
        if ($profilePictureUrl['is_silhouette']) {
            $profilePictureUrl = null;
        } else {
            $profilePictureUrl = $profilePictureUrl['url'];
        }

        return $profilePictureUrl;
    }

    /**
     * get profile data from facebook
     *
     * @param  Facebook\FacebookSession $session facebook session
     * @return array                    profile data
     */
    public function getPublicProfile($session)
    {
        $url = '/me';
        $fbRequest = new FacebookRequest($session, 'GET', $url);
        $publicProfile = $fbRequest->execute()->getGraphObject(GraphUser::className())->asArray();

        return $publicProfile;
    }

    /**
     * merge public profile and profile picture url to one array
     *
     * @param  Facebook\FacebookSession $session facebook session
     * @return array                    profile data with profile picture url
     */
    public function getFullProfile($session)
    {
        $profile = $this->getPublicProfile($session);
        $profile['avatarUrl'] = $this->getProfilePicture($session);

        return $profile;
    }
}
