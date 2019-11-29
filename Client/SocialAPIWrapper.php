<?php
/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 1:25 AM
 */
namespace Client;

use Client\Interfaces\IAPIClient;
use Client\Interfaces\ISocialAPIWrapper;
use Config\Config;
use Exception;
use Karriere\JsonDecoder\JsonDecoder;
use Models\Post;
use Services\Interfaces\IPostProcessorService;
use Services\Interfaces\IResponseService;

header('Content-Type: application/json');
class SocialAPIWrapper implements ISocialAPIWrapper
{
    const BASE_URL = Config::BASE_URL;
    /**
     * @var IAPIClient
     */
    private $client;
    /**
     * @var IResponseService
     */
    private $responseService;
    /**
     * @var IPostProcessorService
     */
    private $postProcessorService;

    /**
     * SocialAPIWrapper constructor.
     * @param IAPIClient $client
     * @param IResponseService $responseService
     * @param IPostProcessorService $postProcessorService
     */
    public function __construct(IAPIClient $client, IResponseService $responseService, IPostProcessorService $postProcessorService)
    {
        $this->client = $client;
        $this->responseService = $responseService;
        $this->postProcessorService = $postProcessorService;
    }

    /**
     * @param string $url
     * @param array $options
     * @param $queryParams
     */
    public function processRequest(string $url, array $options, $queryParams)
    {
        $url = $this->_getFullUrl($url);
        try {
            //validate if query params are correct
            $this->postProcessorService->validateQueryParams($queryParams);
            //check if page is defined, if not then take all posts.
            $hasPosts = $queryParams["page"] !== null ? false : true;
            $allPosts = $this->_getAllPosts($url, $options, $hasPosts);
            //process the stats based on the queried posts
            $stats = $this->postProcessorService->getStats($allPosts);
            //generate an user-friendly message on the response.
            $message = $this->postProcessorService->_formatResponseMessage();
            //return the json response
            echo $this->responseService->multiple(count($stats), $stats,"data",$message);

        } catch (Exception $e) {
            echo $this->responseService
                ->withStatus($e->getCode())
                ->single([], "data", null, $e->getMessage());
        }
    }

    /**
     * @param string $url
     * @return string
     */
    private function _getFullUrl(string $url) : string
    {
        $fullUrl = self::BASE_URL;
        if (!empty($url)) {
            $fullUrl .= $url;
        }
        return $fullUrl;
    }

    /**
     * Check if we need to take all posts or if
     * "page" was defined in the request and
     * return the results accordingly.
     *
     * @param $url
     * @param $options
     * @param $hasPosts
     * @return array
     */
    private function _getAllPosts($url, $options, $hasPosts) : array
    {
        $allPosts = [];
        do {
            //make the api call
            $result = $this->client->get($url, $options);
            //decode the json content
            $jsonContent = json_decode($result->getBody()->getContents());
            //use the JsonDecoder package to get the posts as Post objects
            $jsonDecoder = new JsonDecoder(true);
            $posts = $jsonDecoder->decodeMultiple(json_encode($jsonContent->data->posts), Post::class);
            $allPosts = array_merge($allPosts, $posts);
            $options["page"] += 1;
            //if page is > 10 it means we reached the end of our data
            if ($options["page"] > 10) {
                $hasPosts = false;
            }

        } while ($hasPosts);

        return $allPosts;
    }

    /**
     * Generate a new token when session expired
     *
     * @return string
     */
    public function generateNewToken() : string
    {
        $token = "";
        try {
            $fullUrl = $this->_getFullUrl(Config::AUTH_URL);
            $result = $this->client->post($fullUrl, [
                "client_id" => Config::CLIENT_ID,
                "email" => Config::EMAIL,
                "name" => Config::NAME
            ]);
            $data = json_decode($result->getBody()->getContents());
            $token = $data->data->sl_token;

        } catch (Exception $e) {
            echo $this->responseService
                ->withStatus($e->getCode())
                ->single([], "data", null, $e->getMessage());
        }
        return $token;
    }

}