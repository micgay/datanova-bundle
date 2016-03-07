<?php
namespace Laposte\DatanovaBundle\Provider;

use Exception;
use Psr\Log\LoggerInterface;

class Records
{
    /** Records API endpoint */
    const API_ENDPOINT = '/api/records';

    /** @var string */
    protected $url;

    /** @var LoggerInterface */
    protected $logger;

    /** @var float */
    protected $timeout;

    /**
     * @param string $server
     * @param string $apiVersion
     */
    public function __construct($server, $apiVersion)
    {
        $this->url = sprintf('%s%s/%s', $server, self::API_ENDPOINT, $apiVersion);
    }

    /**
     * @param float $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Set the logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $operation
     * @param array $parameters
     *
     * @return string
     *
     * @throws Exception
     */
    public function get($operation, $parameters = array())
    {
        $this->debug("Records $operation", $parameters);
        $result = null;
        $url = sprintf('%s%s/?%s', $this->url, $operation, http_build_query($parameters));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
        ));
        if (isset($this->timeout)) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        }
        try {
            $response = curl_exec($curl);
            if (!$response) {
                $this->error('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            } else {
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
                $this->logTransferTime($time);
                if (200 === $status) {
                    $result = $response;
                } else {
                    $this->debug('target:  '.$url);
                    $this->logResponseError($status, $response, $parameters);
                }
            }
            curl_close($curl);
        } catch (Exception $exception) {
            $this->debug($exception->getTraceAsString());
            $this->error($exception->getMessage());
        }

        return $result;
    }

    /**
     * @param string $message
     * @param array $context
     */
    private function debug($message, $context = array())
    {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * @param string $time
     *
     * @return array
     */
    private function logTransferTime($time)
    {
        if ($this->logger) {
            $this->logger->debug(sprintf('Transfer time: %.3f sec', $time));
        }
    }

    /**
     * @param int $status
     * @param string $response
     * @param array $parameters
     */
    private function logResponseError($status, $response, $parameters)
    {
        if ($this->logger) {
            $log = sprintf(
                '%d: %s',
                $status,
                $response
            );
            $this->logger->error($log, $parameters);
        }
    }

    /**
     * @param string $message
     * @param array $context
     */
    private function error($message, $context = array())
    {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }
}
