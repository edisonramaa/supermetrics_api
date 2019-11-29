<?php
/**
 * Created by PhpStorm.
 * User: Edison
 * Date: 11/28/2019
 * Time: 1:20 AM
 */

namespace Services;
use Services\Interfaces\IResponseService;

/**
 * Class ResponseService
 */
class ResponseService implements IResponseService
{
    /**
     * @var array $_successStatuses
     */
    protected $_successStatuses = [200, 201];

    /**
     * @var array $_availableFormats
     */
    protected $_availableFormats = ['json'];

    /**
     * @var string $_format
     */
    protected $_format = 'json';

    /**
     * @var int $_status
     */
    protected $_status = 200;

    /**
     * @param int $status
     * @return $this
     */
    public function withStatus($status) : ResponseService
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * @param string $format
     * @return $this
     * @throws \Exception
     */
    public function format($format) : ResponseService
    {
        $this->__checkFormat($format);
        $this->_format = $format;
        return $this;
    }

    /**
     * Returns the results for multiple objects
     *
     * @param int $total
     * @param array $data
     * @param string $key
     * @param string $message
     * @param null $error
     * @return string
     */
    public function multiple($total, $data, $key = '', $message = null, $error = null) : string
    {
        if (empty($key)) {
            $key = "items";
        }

        $data = [
            'total' => $total,
            'success' => in_array($this->_status, $this->_successStatuses),
            $key => $data,
        ];

        if (!is_null($message)) {
            $data['message'] = $message;
        }

        if (!is_null($error)) {
            $data['error'] = $error;
        }

        return $this->_response($data);
    }

    /**
     * Returns the result for single objects
     *
     * @param array $object
     * @param string $key
     * @param string $message
     * @param null $error
     * @return string
     */
    public function single($object, $key = '', $message = null, $error = null) : string
    {
        if (empty($key)) {
            $key = "data";
        }

        $data = [
            'success' => in_array($this->_status, $this->_successStatuses),
        ];

        $data[$key] = $object;

        if (!is_null($message)) {
            $data['message'] = $message;
        }

        if (!is_null($error)) {
            $data['error'] = $error;
        }

        return $this->_response($data);
    }

    /**
     * Returns the json encoded string
     *
     * @param array $data
     * @return string
     */
    protected function _response($data) : string
    {
        return json_encode($data);
    }

    /**
     *
     * Will check is format available for this
     *
     * @param string $format
     * @throws \Exception
     */
    private function __checkFormat($format)
    {
        if (!in_array($format, $this->_availableFormats)) {
            throw new \Exception();
        }
    }
}