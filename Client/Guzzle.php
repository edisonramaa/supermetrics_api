<?php

namespace Client;

use Client\Interfaces\IAPIClient;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 10:44 PM
 */

class Guzzle implements IAPIClient
{

    public function client()
    {
        return new Client();
    }

    /**
     * @param string $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        $options = $this->_formatGetParameters($options);
        return $this->client()->get($url,$options);
    }

    /**
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function post(string $url, array $options = []): ResponseInterface
    {
        $options = $this->_formatRequestParameters($options);
        return $this->client()->post($url, $options);
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function _formatGetParameters(array $parameters): array
    {
        return ['query' => $parameters];
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function _formatRequestParameters(array $parameters): array
    {
        return ['form_params' => $parameters];
    }

}