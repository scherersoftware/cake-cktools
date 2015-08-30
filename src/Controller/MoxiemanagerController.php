<?php
namespace CkTools\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Hash;

class MoxiemanagerController extends AppController
{

    /**
     * Wrap Moxiemanager's api.php in a controller action.
     *
     * @return void
     */
    public function api()
    {
        try {
            $pluginPath = Plugin::path('CkTools');

            define('MOXMAN_CLASSES', $pluginPath . 'src/Lib/moxiemanager/classes');
            define('MOXMAN_PLUGINS', $pluginPath . 'src/Lib/moxiemanager/plugins');
            define('MOXMAN_ROOT', $pluginPath . 'src/Lib/moxiemanager');
            define('MOXMAN_API_FILE', __FILE__);

            $appConfig = Configure::read('CkTools.moxiemanager');
            Configure::load('CkTools.moxiemanager');
            $moxieManagerConfig = Configure::read('moxiemanager');
            if (is_array($appConfig)) {
                $moxieManagerConfig = Hash::merge($moxieManagerConfig, $appConfig);
            }

            $GLOBALS['moxieManagerConfig'] = $moxieManagerConfig;
            require_once MOXMAN_CLASSES . '/MOXMAN.php';

            $context = \MOXMAN_Http_Context::getCurrent();
            $pluginManager = \MOXMAN::getPluginManager();

            foreach ($pluginManager->getAll() as $plugin) {
                if ($plugin instanceof \MOXMAN_Http_IHandler) {
                    $plugin->processRequest($context);
                }
            }
        } catch (Exception $e) {
            \MOXMAN_Exception::printException($e);
        }
        return $this->render(false, false);
    }
}
