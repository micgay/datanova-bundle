<?php

namespace Laposte\DatanovaBundle\Service;

use Laposte\DatanovaBundle\Provider\ClientInterface;

class Downloader
{
    /** @var ClientInterface $client */
    private $client;
    /** @var  Finder $finder */
    private $finder;

    /**
     * @param ClientInterface $client
     * @param Finder $finder
     */
    public function __construct(ClientInterface $client, Finder $finder)
    {
        $this->client = $client;
        $this->finder = $finder;
    }

    public function download($dataset, $format, $filter = null, $updateExisting = false)
    {
        $parameters = array(
            'dataset' => $dataset,
            'format' => $format
        );
        if (isset($filter)) {
            $parameters['q'] = $filter;
        }
        $content = $this->client->get('download', $parameters);

        return $this->finder->save($dataset, $content, $format, $updateExisting);
    }

    /**
     * @param string $dataset
     * @param string $format
     *
     * @return bool
     */
    public function exists($dataset, $format)
    {
        return $this->finder->exists($dataset, $format);
    }
}