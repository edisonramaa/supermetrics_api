<?php


namespace Client\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 10:41 PM
 **/

interface IAPIClient
{

    public function client();

    /**
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function get(string $url, array $options = []): ResponseInterface;

    /**
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function post(string $url, array $options = []): ResponseInterface;


}