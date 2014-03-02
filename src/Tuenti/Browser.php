<?php

namespace Tuenti;

class Browser
{
    public function post($url, $parameters, $headers)
    {
        $context = $this->prepareContext($parameters, $headers);

        return file_get_contents($url, false, $context);
    }

    private function prepareContext($parameters, $headers)
    {
        return stream_context_create(array('http' => array(
             'method' => 'POST',
             'header' => $this->buildHeaders($headers),
             'content' => $parameters,
        )));
    }

    private function buildHeaders($headers)
    {
        $h = '';
        foreach ($headers as $name => $value) {
            $h .= $name . ': ' . $value . "\r\n";
        }
        return $h;
    }
}