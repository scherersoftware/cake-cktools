<?php
namespace CkTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Response;
use Cake\Utility\Hash;
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
    protected $_defaultConfig = [
        'jsonEncodeOptions' => JSON_UNESCAPED_SLASHES
    ];

    /**
     * Holds the Response object
     *
     * @var Response
     */
    protected $_response = null;

    /**
     * Maps return codes to HTTP status codes
     *
     * @var array
     */
    protected $_statusCodeMapping = [];

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_statusCodeMapping = ApiReturnCode::getStatusCodeMapping();
    }

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
    public function response($returnCode = ApiReturnCode::SUCCESS, array $data = [], $httpStatusCode = null)
    {
        if (!$httpStatusCode) {
            $httpStatusCode = $this->getHttpStatusForReturnCode($returnCode);
        }

        $response = $this->getResponse();
        $response->statusCode($httpStatusCode);

        $responseData = [
            'code' => $returnCode,
            'data' => $data
        ];
        $response->type('json');
        $response->body(json_encode($responseData, $this->config('jsonEncodeOptions')));

        return $response;
    }

    /**
     * Returns the appropriate HTTP Status code for the given return code.
     *
     * @param string $returnCode Return Code
     * @return int
     */
    public function getHttpStatusForReturnCode($returnCode)
    {
        if (!isset($this->_statusCodeMapping[$returnCode])) {
            throw new \Exception("Return code {$returnCode} is not mapped to any HTTP Status Code.");
        }
        return $this->_statusCodeMapping[$returnCode];
    }

    /**
     * Obtain the status code mapping
     *
     * @return array
     */
    public function getStatusCodeMapping()
    {
        return $this->_statusCodeMapping;
    }

    /**
     * Map a return code to a status code
     *
     * @param string $returnCode Return Code
     * @param int $httpStatusCode The HTTP Status code to use for the given return code
     * @return void
     */
    public function mapStatusCode($returnCode, $httpStatusCode)
    {
        $this->_statusCodeMapping[$returnCode] = $httpStatusCode;
    }

    /**
     * Map return codes to HTTP Status codes
     *
     * @param array $codes Array with the return code as key and the HTTP Status code as value
     * @return void
     */
    public function mapStatusCodes(array $codes)
    {
        $this->_statusCodeMapping = Hash::merge($this->getStatusCodeMapping(), $codes);
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
