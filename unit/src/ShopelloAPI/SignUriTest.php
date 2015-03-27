<?php
namespace Tests\Shopello\API;

use \Shopello\API\SignUri;

class SignUriTest extends \PHPUnit_Framework_TestCase
{
    /** @var SignUri */
    private $target;



    public function setUp()
    {
        $this->target = new SignUri();
    }



    /**
     * @test
     */
    public function shouldSignUri()
    {
        // Fixture
        $uri = 'https://example.com/';
        $secret = 123456789;
        $paramName = 'clickdata';
        $params = array(1111, 2222);

        // Test
        $actual = $this->target->signUri($uri, $secret, $paramName, $params);

        // Assert
        $this->assertSame('https://example.com/?clickdata=1077e65030.WzExMTEsMjIyMl0', $actual);
    }



    /**
     * @test
     */
    public function shouldExtractData()
    {
        // Fixture
        $uri = 'https://example.com/?clickdata=1077e65030.WzExMTEsMjIyMl0';
        $secret = 123456789;
        $paramName = 'clickdata';

        // Test
        $actual = $this->target->verifySignature($uri, $secret, $paramName);

        // Assert
        $this->assertSame(array(1111, 2222), $actual);
    }



    /**
     * @test
     */
    public function shouldFailOnMissingGetParam()
    {
        // Fixture
        $uri = 'https://example.com/';
        $secret = 123456789;
        $paramName = 'clickdata';

        // Test
        $actual = $this->target->verifySignature($uri, $secret, $paramName);

        // Assert
        $this->assertFalse($actual);
    }



    /**
     * @test
     */
    public function shouldFailOnAlteredSignature()
    {
        // Fixture
        $uri = 'https://example.com/?clickdata=10.WzExMTEsMjIyMl0';
        $secret = 123456789;
        $paramName = 'clickdata';

        // Test
        $actual = $this->target->verifySignature($uri, $secret, $paramName);

        // Assert
        $this->assertFalse($actual);
    }
}
