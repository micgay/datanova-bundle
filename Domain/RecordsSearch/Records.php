<?php
namespace OpenData\LaPoste\DataNOVABundle\Domain\RecordsSearch;

use Exception;
use GuzzleHttp\Client;
use OpenData\LaPoste\DataNOVABundle\Domain\Authenticator;
use Psr\Log\LoggerInterface;

class Records
{
    const API_ENDPOINT = '/api/records/1.0/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Client $client Guzzle http client
     * @param Authenticator $authenticator  authenticator service
     */
    public function __construct(Client $client, Authenticator $authenticator)
    {
        $this->client = $client;
        $this->authenticator = $authenticator;
    }

    /**
     * Set the logger
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $search
     *
     * @return string|false|null
     * @throws Exception
     */
    public function get($search)
    {
        if ($this->logger) {
            $this->logger->debug("Provider::get()");
        }
        $uri = sprintf(
            '%s/%s',
            self::API_ENDPOINT,
            $search
        );
        $response = $this->fetch('GET', $uri);

        return $response->getBody()->getContents();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function fetch($method, $uri = null, array $options = [])
    {
        if (empty($options['headers']['Authorization'])) {
            $options['headers']['Authorization'] = $this->getAuthorizationHeader();
        }
        $response = $this->client->request($method, $uri, $options);
        switch ($response->getStatusCode()) {
            case 404:
                if ($this->logger) {
                    $this->logger->warning('Not found.');
                }
                break;
            case 400:
                if ($this->logger) {
                    $this->logger->warning('Bad request.');
                }
                break;
        }

        return $response;
    }

    /**
     * Return Authorization header value (Bearer token)
     *
     * @param bool|false $forceReauth if set to true, it will request for a new token although it is already available
     *
     * @return string
     */
    private function getAuthorizationHeader($forceReauth = false)
    {
        if ($forceReauth || null === $this->authenticator->getToken()) {
            $this->authenticator->authenticate();
        }
        return "Bearer {$this->authenticator->getToken()}";
    }
}
