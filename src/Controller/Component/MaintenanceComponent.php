<?php
declare(strict_types = 1);
namespace CkTools\Controller\Component;

use App\Lib\Environment;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Cookie\Cookie;

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
     * @param \Cake\Controller\ComponentRegistry $registry A ComponentRegistry object.
     * @param array                              $config   Array of configuration settings.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        $this->_controller = $this->getController();
    }

    /**
     * Returns if the maintenance mode is currently active
     *
     * @return bool
     */
    public static function isMaintenanceActive(): bool
    {
        return (bool)Environment::read('MAINTENANCE');
    }

    /**
     * Maintenance redirect logic
     *
     * @return mixed
     */
    public function beforeFilter()
    {
        if (defined('PHPUNIT_TESTSUITE')) {
            return;
        }

        $maintenancePage = Environment::read('MAINTENANCE_PAGE_REDIRECT_URL');
        $currentUrl = $this->_controller->getRequest()->getAttribute('here');
        $accessibleUrls = explode('|', Environment::read('MAINTENANCE_ACCESSIBLE_URLS'));
        $accessibleUrls[] = $maintenancePage;

        if (!self::isMaintenanceActive()) {
            // if maintenance is not active but maintenance page is requested -> redirect to default page
            if (
                in_array($currentUrl, $accessibleUrls)
                && substr($maintenancePage, -strlen($currentUrl)) === $currentUrl
            ) {
                $maintenanceBasePage = Environment::read('MAINTENANCE_BASE_URL');

                return $this->_controller->redirect($maintenanceBasePage);
            }

            return;
        }

        $cookieName = Environment::read('MAINTENANCE_COOKIE_NAME');
        $cookieExists = ($this->_controller->getRequest()->getCookie($cookieName) !== null);
        if ($cookieExists) {
            return;
        }
        $headerActive = Environment::read('MAINTENANCE_HEADER_ACTIVE');
        $headerName = Environment::read('MAINTENANCE_HEADER_NAME');
        $headerValue = Environment::read('MAINTENANCE_HEADER_VALUE');
        $successUrl = Environment::read('MAINTENANCE_PASSWORD_SUCCESS_URL');
        $cookie = new Cookie($cookieName, '1');
        if (
            $headerActive
            && !empty($this->_controller->getRequest()->getHeader($headerName))
            && $this->_controller->getRequest()->getHeader($headerName) === $headerValue
        ) {
            $this->_controller->getRequest()->getCookieCollection()->add($cookie);

            return $this->_controller->redirect($successUrl);
        }
        $passwordUrl = Environment::read('MAINTENANCE_PASSWORD_URL');
        $accessibleUrls[] = $passwordUrl;
        if (!in_array($currentUrl, $accessibleUrls)) {
            return $this->_controller->redirect($maintenancePage);
        }
        if ($currentUrl != $passwordUrl) {
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
            }

            if ($_SERVER['PHP_AUTH_USER'] === $user && $_SERVER['PHP_AUTH_PW'] === $password) {
                $this->_controller->getRequest()->getCookieCollection()->add($cookie);

                return $this->_controller->redirect($successUrl);
            }

            return $this->_controller->redirect($maintenancePage);
        }

        return $this->_controller->redirect($maintenancePage);
    }
}
