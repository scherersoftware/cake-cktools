<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Response;
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
     * Holds the Response object
     *
     * @var Response
     */
    protected $_response = null;

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
     * Returns the response object to modify
     *
     * @return Response
     */
    public function getResponse()
    {
        if ($this->_response) {
            return $this->_response;
        }
        return $this->_registry->getController()->response;
    }

    /**
     * Set the response object for manipulation by response()
     *
     * @param Response $response Response object to manipulate
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
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
        $response = $this->getResponse();
        $response->statusCode($httpStatusCode);

        $responseData = [
            'code' => $returnCode,
            'data' => $data
        ];
        $response->type('json');
        $response->body(json_encode($responseData));

        return $response;
    }

    /**
     * Generates a unique API token
     *
     * @return string
     */
    public function generateApiToken()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}
