<?php
namespace LaPoste\DataNovaBundle\Provider;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class Records
{
    const API_ENDPOINT = '/api/records';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
        if ($this->logger) {
            $this->logger->debug("Records $operation", $parameters);
        }
        $uri = sprintf('%s%s/', $this->url, $operation);
        $config = array(
            'http_errors' => false,
            'query' => $parameters
        );
        $response = $this->client->get($uri, $config);
        switch ($response->getStatusCode()) {
            case 400:
                if ($this->logger) {
                    $this->logger->error('Bad request', $parameters);
                }
                break;
            case 404:
                if ($this->logger) {
                    $this->logger->error('Not found', $parameters);
                }
                break;
        }

        return $response->getBody()->getContents();
    }
}
