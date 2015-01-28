<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use CkTools\Lib\ApiReturnCode;

/**
 * Api component
 */
class ApiComponent extends Component
{

    /**
     * Used Components
     *
     * @var array
     */
    public $components = [
        'RequestHandler'
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Should be called in the controller's beforeFilter callback
     *
     * @return void
     */
    public function setup()
    {
        $this->RequestHandler->addInputType('json', ['json_decode', true]);
        $this->RequestHandler->prefers('json');
        // Force a JSON response regardless of extension
        // $this->RequestHandler->renderAs($this->_registry->getController(), 'json');
    }

    /**
     * Returns a standartized JSON response
     *
     * @param int $httpStatusCode HTTP Status Code to send
     * @param string $returnCode A string code more specific to the result
     * @param array $data Data for the 'data' key
     * @return Response
     */
    public function response($httpStatusCode = 200, $returnCode = ApiReturnCode::SUCCESS, array $data = [])
    {
        $response = $this->_registry->getController()->response;
        $response->statusCode($httpStatusCode);

        $responseData = [
            'code' => $returnCode,
            'data' => $data
        ];
        $response->type('json');
        $response->body(json_encode($responseData));

        return $response;
    }
}
