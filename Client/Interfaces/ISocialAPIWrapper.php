<?php
/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 2:37 AM
 */

namespace Client\Interfaces;

interface ISocialAPIWrapper
{
    /**
     * @param string $url
     * @param array $options
     * @param $queryParams
     * @return mixed
     */
    public function processRequest(string $url, array $options, $queryParams);
}