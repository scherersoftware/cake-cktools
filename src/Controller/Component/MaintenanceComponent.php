<?php
namespace CkTools\Controller\Component;

use App\Lib\Environment;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Maintenance component
 */
class MaintenanceComponent extends Component
{
    /**
     * @var \Cake\Controller\Controller
     */
    protected $_controller;


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
            $this->_controller = $registry->getController();
        }
    }

    /**
     * maintenance redirect logic
     *
     * @return mixed|void
     */
    public function beforeFilter()
    {
        if (defined('PHPUNIT_TESTSUITE')) {
            return;
        }
        $activated = Environment::read('MAINTENANCE');
        if (!$activated) {
            return;
        }
        $cookieName = Environment::read('MAINTENANCE_COOKIE_NAME');
        $cookieExists = ($this->_controller->Cookie->read($cookieName) != null);
        if ($cookieExists) {
            return;
        }
        $headerActive = Environment::read('MAINTENANCE_HEADER_ACTIVE');
        $headerName = Environment::read('MAINTENANCE_HEADER_NAME');
        $headerValue = Environment::read('MAINTENANCE_HEADER_VALUE');
        $successUrl = Environment::read('MAINTENANCE_PASSWORD_SUCCESS_URL');
        if ($headerActive && !empty($this->request->header($headerName)) && $this->request->header($headerName) == $headerValue) {
            $this->_controller->Cookie->write($cookieName, true);
            return $this->_controller->redirect($successUrl);
        }
        $currentUrl = $this->request->here;
        $accessibleUrls = explode('|', Environment::read('MAINTENANCE_ACCESSIBLE_URLS'));
        $maintenancePage = Environment::read('MAINTENANCE_PAGE_REDIRECT_URL');
        $accessibleUrls[] = $maintenancePage;
        $passwordUrl = Environment::read('MAINTENANCE_PASSWORD_URL');
        $accessibleUrls[] = $passwordUrl;
        if (!in_array($currentUrl, $accessibleUrls)) {
            return $this->_controller->redirect($maintenancePage);
        } elseif ($currentUrl != $passwordUrl) {
            return;
        }
        $user = Environment::read('MAINTENANCE_USER');
        $password = Environment::read('MAINTENANCE_PASSWORD');
        if ($currentUrl == $passwordUrl) {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="Maintenance Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Unauthorized';
                exit;
            } else {
                if ($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $password) {
                    $this->_controller->Cookie->write($cookieName, true);
                    return $this->_controller->redirect($successUrl);
                }
                return $this->_controller->redirect($maintenancePage);
            }
        }
        return $this->_controller->redirect($maintenancePage);
    }
}
