<?php
/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 2:45 AM
 */

namespace Services\Interfaces;


interface IPostProcessorService
{
    /**
     * @param $queryParams
     * @return bool
     */
    public function validateQueryParams($queryParams) : bool;

    /**
     * @param $posts
     * @return array
     */
    public function getStats($posts) : array;

    public function _formatResponseMessage() : string;
}