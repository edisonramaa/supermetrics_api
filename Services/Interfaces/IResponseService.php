<?php

namespace Services\Interfaces;

use Services\ResponseService;

/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 1:29 AM
 */

interface IResponseService
{
    /**
     * @param $format
     * @return ResponseService
     */
    public function format($format) : ResponseService;

    /**
     * @param $total
     * @param $data
     * @param string $key
     * @param null $message
     * @param null $error
     * @return mixed
     */
    public function multiple($total, $data, $key = '', $message = null, $error = null);

    /**
     * @param $object
     * @param string $key
     * @param null $message
     * @param null $error
     * @return string
     */
    public function single($object, $key = '', $message = null, $error = null) : string;

    /**
     * @param $status
     * @return ResponseService
     */
    public function withStatus($status) : ResponseService;

}