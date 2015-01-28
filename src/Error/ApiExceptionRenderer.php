<?php
namespace CkTools\Error;

class ApiExceptionRenderer extends \Cake\Error\ExceptionRenderer
{

    /**
     * overwriting get controller
     *
     * @return Controller instance of controller
     */
    protected function _getController()
    {
        $controller = parent::_getController();
        $controller->loadComponent('RequestHandler');
        $controller->RequestHandler->renderAs($controller, 'json');
        return $controller;
    }
}
