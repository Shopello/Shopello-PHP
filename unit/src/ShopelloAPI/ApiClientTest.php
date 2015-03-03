<?php
namespace Tests\Shopello\API;

use \Shopello\API\ApiClient;
use \Curl\Curl;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApiClient */
    private $target;

    /** @var Curl */
    private $curlMock;

    public function setUp()
    {
        $this->curlMock = $this->getMock('\Curl\Curl');

        $this->target = new ApiClient($this->curlMock);
    }

    /**
     * @test
     */
    public function shouldSetApiKey()
    {
        // Fixture
        // Test
        $actual = $this->target->setApiKey('Hello');

        // Assert
        $this->assertSame(null, $actual);
    }

    /**
     * @test
     */
    public function shouldSetApiEndpoint()
    {
        // Fixture
        // Test
        $actual = $this->target->setApiEndpoint('Hello');

        // Assert
        $this->assertSame(null, $actual);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function shouldFailRequest()
    {
        // Fixate
        $this->curlMock->response = json_encode(
            (object) array(
                'error' => 'Not allowed'
            )
        );

        $this->curlMock->error = 1;

        // Test
        $actual = $this->target->getProduct(1);
    }

    /**
     * @test
     */
    public function shouldCallCall()
    {
        $actual01 = $this->target->getCategory(1);
        $actual02 = $this->target->getCategories();
        $actual03 = $this->target->getCategoryParents();
        $actual04 = $this->target->getProducts();
        $actual05 = $this->target->getProduct(1);
        $actual06 = $this->target->getProductPriceHistory(array('product_id' => 1));
        $actual07 = $this->target->getRelatedProducts(1);
        $actual08 = $this->target->getBrands();
        $actual09 = $this->target->getStores();
        $actual10 = $this->target->getStore(1);
        $actual11 = $this->target->getCategoryTree(1);
    }
}
