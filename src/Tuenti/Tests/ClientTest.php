<?php

namespace Tuenti\Tests;

use Tuenti\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $browser;

    protected function setUp()
    {
        $this->client = new Client('email', 'password');
        $this->browser = new FakeBrowser;
        $this->client->setBrowser($this->browser);
    }

    /**
     * @test
     */
    public function shouldAuthenticate()
    {
        $this->browserIsPreparedForAuthentication();

        $me = $this->client->me();

        $this->assertEquals('id', $me);
        $this->assertBrowserHeadersAreValid();
        $this->assertBrowserReceivedValidAuthenticationDetails();
    }

    private function browserIsPreparedForAuthentication()
    {
        $this->browser
            ->returns('getChallenge', '[{"challenge":"c","seed":"f","timestamp":1}]')
            ->returns('getSession', '[{"user_id":"id"}]');
    }

    private function assertBrowserHeadersAreValid()
    {
        $expectedHeaders = array(
            'Accept' => '*/*',
            'Accept-Language' => 'es-es',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Tuenti/1.2 CFNetwork/485.10.2 Darwin/10.3.1',
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $this->assertEquals($expectedHeaders, $this->browser->requests['getChallenge']['headers']);
    }

    private function assertBrowserReceivedValidAuthenticationDetails()
    {
        $expectedSessionRequest =
            '{"version":"0.5","requests":[["getSession",{' .
            '"passcode":"420986589fa018302702d5c11b0460b9","seed":"f","email":"email","timestamp":1,' .
            '"application_key":"MDI3MDFmZjU4MGExNWM0YmEyYjA5MzRkODlmMjg0MTU6MC43NzQ4ODAwMCAxMjc1NDcyNjgz"' .
            '}]]}';
        $this->assertEquals($expectedSessionRequest, $this->browser->requests['getSession']['parameters']);
    }

    /**
     * @test
     */
    public function shouldCacheSession()
    {
        $this->browserIsPreparedForAuthentication();

        $this->client->me();
        $this->client->me();

        $this->assertEquals(2, $this->browser->numberOfRequests);
    }

    /**
     * @test
     * @expectedException Tuenti\ApiError
     */
    public function responseWithErrorShouldThrowException()
    {
        $this->browser->returns('getChallenge', '[{"error":32,"message":"Test error"}]');

        $this->client->me();
    }
}