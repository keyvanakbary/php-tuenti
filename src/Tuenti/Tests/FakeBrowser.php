<?php

namespace Tuenti\Tests;

class FakeBrowser
{
    public $requests = array();
    private $responses = array();
    public $numberOfRequests = 0;

    public function returns($methodName, $value)
    {
        $this->responses[$methodName] = $value;

        return $this;
    }

    public function post($url, $parameters, $headers)
    {
        $request = json_decode($parameters, true);

        $this->numberOfRequests++;

        $methodName = $request['requests'][0][0];
        $this->requests[$methodName] = array(
            'url' => $url,
            'parameters' => $parameters,
            'headers' => $headers
        );

        return $this->responses[$methodName];
    }
}