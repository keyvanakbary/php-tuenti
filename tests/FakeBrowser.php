<?php

class FakeBrowser
{
    public $url = '';
    public $parameters = '';
    public $headers = array();
    public $numberOfRequests = 0;

    private $responses = array();
    private $fixedReturn;

    public function returns($methodName, $value)
    {
        $this->responses[$methodName] = $value;

        return $this;
    }

    public function alwaysReturn($value)
    {
        $this->fixedReturn = $value;
    }

    public function post($url, $parameters, $headers)
    {
        $this->url = $url;
        $this->parameters = $parameters;
        $this->headers = $headers;
        $this->numberOfRequests++;

        return $this->guessResponseFor($parameters);
    }

    private function guessResponseFor($parameters)
    {
        $methodName = $this->methodFor($parameters);

        return (isset($this->responses[$methodName])) ? $this->responses[$methodName] : $this->fixedReturn;
    }

    private function methodFor($call)
    {
        $request = json_decode($call, true);

        return $request['requests'][0][0];
    }
}