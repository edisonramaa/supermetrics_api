<?php
/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 2:45 AM
 */

//test

namespace Services;

use Config\Config;
use DateTime;
use Models\Post;
use Services\Interfaces\IPostProcessorService;

class PostProcessorService implements IPostProcessorService
{
    /**
     * @var array $_period
     */
    protected $_periodOptions = [Config::PERIOD_MONTH, Config::PERIOD_WEEK];

    /**
     * @var string $_periodSelected
     */
    protected $_periodSelected;


    /**
     * @var array $_identifier
     */
    protected $_identifierOptions = [Config::IDENTIFIER_CHARACTER, Config::IDENTIFIER_POST];

    /**
     * @var string $_identifierSelected
     */
    protected $_identifierSelected;

    /**
     * @var array $_statOptions
     */
    protected $_statOptions = [Config::STAT_OPTION_AVERAGE, Config::STAT_OPTION_LONGEST, Config::STAT_OPTION_TOTAL];

    /**
     * @var string $_statSelected
     */
    protected $_statSelected;

    /**
     * Validate if the queryParams taken from the request
     * are valid and if we can proceed with the request.
     *
     * @param $queryParams
     * @return bool
     * @throws \Exception
     */
    public function validateQueryParams($queryParams) : bool
    {
        if (!in_array($queryParams[Config::PERIOD_INDEX], $this->_periodOptions)) {
            throw new \Exception("Period is not valid. Please choose between month|week.");
        } elseif (!in_array($queryParams[Config::IDENTIFIER_INDEX], $this->_identifierOptions)) {
            throw new \Exception("Identifier is not valid. Please choose between character|post.");
        } elseif (!in_array($queryParams[Config::STAT_OPTION_INDEX], $this->_statOptions)) {
            throw new \Exception("statOption is not valid. Please choose between average|longest|total.");
        } elseif ($queryParams[Config::STAT_OPTION_INDEX] == Config::STAT_OPTION_LONGEST &&
            ($queryParams[Config::IDENTIFIER_INDEX] !== Config::IDENTIFIER_POST || $queryParams[Config::PERIOD_INDEX] !== Config::PERIOD_MONTH)) {
            throw new \Exception("Unsupported identifier or period. Please choose identifier ".Config::IDENTIFIER_POST." and period ".Config::PERIOD_MONTH." to proceed or change statOption.");
        } else {
            $this->_periodSelected = $queryParams[Config::PERIOD_INDEX];
            $this->_statSelected = $queryParams[Config::STAT_OPTION_INDEX];
            $this->_identifierSelected = $queryParams[Config::IDENTIFIER_INDEX];
            return true;
        }
    }

    /**
     * @param $posts
     * @return array
     */
    public function getStats($posts): array
    {
        $dataByPeriod = $this->_getDataByPeriod($posts);
        $stats = $this->_generateStats($dataByPeriod);
        return $stats;
    }

    /**
     * Separate the array into specific period
     * weeks of the year or months of the year
     * and return the new array
     *
     * @param array $posts
     * @return array
     */
    protected function _getDataByPeriod(array $posts) : array
    {
        $postsByPeriod = [];
        foreach ($posts as $post) {
           $date =  \DateTime::createFromFormat(DateTime::ISO8601, $post->created_time);
           $periodType = $this->_periodSelected == Config::PERIOD_MONTH ? "M" : "W";
           $periodFormat = $date->format($periodType);
           $postsByPeriod[$periodFormat][] = $post;
        }
        return $postsByPeriod;
    }

    /**
     * Generate the stats according to
     * the query parameters sent
     *
     * @param array $dataByPeriod
     * @return array
     */
    protected function _generateStats(array $dataByPeriod) : array
    {
        $dataByIdentifier = [];
        foreach ($dataByPeriod as $periodIndex => $data) {
            $dataByIdentifier[$periodIndex][$this->_periodSelected] = $periodIndex;
            if ($this->_statSelected == Config::STAT_OPTION_AVERAGE) {
                if ($this->_identifierSelected == Config::IDENTIFIER_POST) {
                    $dataByIdentifier[$periodIndex]["average"] = $this->_getAverageNumberOfPosts($data);
                } else {
                    $dataByIdentifier[$periodIndex]["average"] = $this->_getAverageCharacterLength($data);
                }
            } elseif ($this->_statSelected == Config::STAT_OPTION_TOTAL) {
                $dataByIdentifier[$periodIndex]["total"] = $this->_getTotalByIdentifier($data);
            } elseif ($this->_statSelected == Config::STAT_OPTION_LONGEST) {
                $dataByIdentifier[$periodIndex]["longest_post"] = $this->_getLongestPost($data);
            }
        }
        return array_values($dataByIdentifier);
    }

    /**
     * Get the average number of posts
     * by user
     *
     * @param array $data
     * @return float
     */
    protected function _getAverageNumberOfPosts(array $data) : float
    {
        $users = [];
        foreach ($data as $post) {
            if (!in_array($post->from_id, $users)) {
                $users[] = $post->from_id;
            }
        }
        return round(count($data) / count($users), 2);
    }

    /**
     * Get average character length for posts
     *
     * @param array $data
     * @return float
     */
    protected function _getAverageCharacterLength(array $data) : float
    {
        $characterCount = 0;
        foreach ($data as $post) {
            $characterCount += strlen($post->message);
        }
        return round($characterCount / count($data), 2);
    }

    /**
     * Get longest post based on
     * their character length
     *
     * @param $data
     * @return Post
     */
    protected function _getLongestPost($data) : Post
    {
        $longestPost = null;
        foreach ($data as $post) {
            if ($longestPost == null || strlen($post->message) > strlen($longestPost->message)) {
                $longestPost = $post;
            }
        }
        return $longestPost;
    }

    /**
     * Get total number of items based on the
     * selected identifier
     *
     * @param array $data
     * @return int
     */
    protected function _getTotalByIdentifier(array $data) : int
    {
        if ($this->_identifierSelected == Config::IDENTIFIER_POST) {
            return count($data);
        } else {
            $totalChars = 0;
            foreach ($data as $post) {
                $totalChars += strlen($post->message);
            }
            return $totalChars;
        }
    }

    /**
     * Get message for the final json response
     * based on the query selections
     *
     * @return string
     */
    public function _formatResponseMessage() : string
    {
        $message = ucfirst($this->_statSelected)." ".$this->_identifierSelected."s per ".
            $this->_periodSelected." retrieved successfully.";

        return $message;
    }
}