<?php
use Cake\Routing\Router;

Router::plugin('CkTools', function ($routes) {
	$routes->fallbacks();
});
