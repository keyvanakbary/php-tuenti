<?php

namespace Tuenti\Tests;

class FakeBrowser
{
    public $requests = array();
    public $numberOfRequests = 0;

    private $responses = array();
    private $fixedReturn;

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

        return (isset($this->responses[$methodName])) ? $this->responses[$methodName] : $this->fixedReturn;
    }

    public function alwaysReturn($value)
    {
        $this->fixedReturn = $value;
    }

    public function getSentParameters($methodName)
    {
        $requests = json_decode($this->requests[$methodName]['parameters'], true);

        return  $requests['requests'][0];
    }
}