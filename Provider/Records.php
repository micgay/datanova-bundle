<?php
namespace LaPoste\DataNovaBundle\Provider;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class Records
{
    /** Records API endpoint */
    const API_ENDPOINT = '/api/records';

    /** @var Client */
    protected $client;

    /** @var string */
    protected $url;

    /** @var LoggerInterface */
    protected $logger;

    /** @var array */
    protected $config;

    /**
     * @param Client $client Guzzle http client
     * @param string $apiVersion
     */
    public function __construct(Client $client, $apiVersion)
    {
        $this->client = $client;
        $this->url = sprintf('%s/%s', self::API_ENDPOINT, $apiVersion);
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
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
        $config = $this->config;
        $config = $this->setTransferTimeLog($config);
        $config['query'] = $parameters;
        $uri = sprintf('%s%s/', $this->url, $operation);
        try {
            $response = $this->client->get($uri, $config);
            if (200 === $response->getStatusCode()) {
                $result = $response->getBody()->getContents();
            } else {
                $this->logResponseError($response, $config);
            }
        } catch (Exception $exception) {
            $this->debug($exception->getTraceAsString());
            $this->error($exception->getMessage(), $config);
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
     * @param array $config
     *
     * @return array
     */
    private function setTransferTimeLog($config = array())
    {
        if ($this->logger) {
            $config['on_stats'] = function (TransferStats $stats) {
                $this->logger->debug(sprintf('Transfer time: %.3f sec', $stats->getTransferTime()));
            };
        }

        return $config;
    }

    /**
     * @param ResponseInterface $response
     * @param $config
     */
    private function logResponseError(ResponseInterface $response, $config)
    {
        if ($this->logger) {
            $log = sprintf(
                '%d %s: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getBody()->getContents()
            );
            $this->logger->error($log, $config);
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
