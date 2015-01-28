<?php
namespace CkTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use CkTools\Controller\Component\ApiComponent;
use CkTools\Lib\ApiReturnCode;

/**
 * Api\Controller\Component\ApiComponent Test Case
 */
class ApiComponentTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Api = new ApiComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Api);

        parent::tearDown();
    }

    /**
     * Test generateApiToken()
     *
     * @return void
     */
    public function testGenerateApiToken()
    {
        $token = $this->Api->generateApiToken();
        $this->assertTrue(is_string($token));
    }

    /**
     * Test the response() method.
     *
     * @return void
     */
    public function testResponse()
    {
        $cakeResponse = new Response();
        $this->Api->setResponse($cakeResponse);

        $httpStatus = 201;
        $code = ApiReturnCode::SUCCESS;
        $data = [
            'foo' => 'bar'
        ];
        $response = $this->Api->response($httpStatus, $code, $data);

        $this->assertEquals($response->type(), 'application/json');
        $this->assertEquals($response->statusCode(), $httpStatus);
        
        $decoded = json_decode($response->body(), true);
        $this->assertEquals($decoded['data'], $data);
        $this->assertEquals($decoded['code'], $code);
    }
}
