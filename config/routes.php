<?php
use Cake\Routing\Router;

Router::plugin('CkTools', function ($routes) {
    /**
     * This is necessary because of constrained configuration possibility in
     * the JS implementation of Moxiemanager.
     */
    $routes->connect('/js/vendor/moxiemanager/api.php', [
        'plugin' => 'CkTools',
        'controller' => 'Moxiemanager',
        'action' => 'api'
    ]);
    $routes->fallbacks('DashedRoute');
});
