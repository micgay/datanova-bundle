<?php

namespace Laposte\DatanovaBundle\Manager;

use Laposte\DatanovaBundle\Model\Download;
use Laposte\DatanovaBundle\Model\Search;
use Laposte\DatanovaBundle\Provider\ClientInterface;
use Laposte\DatanovaBundle\Service\Downloader;
use Laposte\DatanovaBundle\Service\Finder;
use Laposte\DatanovaBundle\Service\Parser\ParserInterface;
use Psr\Log\LoggerInterface;

class RecordsManager
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var ParserInterface[]
     */
    private $parsers;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param ClientInterface $client
     * @param Downloader $downloader
     * @param Finder $finder
     */
    public function __construct(ClientInterface $client, Downloader $downloader, Finder $finder)
    {
        $this->parsers = array();
        $this->client = $client;
        $this->downloader = $downloader;
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ParserInterface $parser
     */
    public function addParser(ParserInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @param Search $search
     *
     * @return array|false
     */
    public function search(Search $search)
    {
        //look locally (parser)
        $data = $this->searchLocally($search);
        //query remote api
        if (false === $data) {
            $data = $this->searchDistant($search);
        }

        return $data;
    }

    /**
     * Look locally (parsers)
     *
     * @param Search $search
     *
     * @return array|false
     */
    private function searchLocally(Search $search)
    {
        $this->log('debug', sprintf('Search locally for %s dataset', $search->getDataset()), $search->getParameters());
        $result = false;
        foreach ($this->parsers as $parser) {
            $parsed = $parser->parse($search->getDataset());
            if ($parsed) {
                $result = $this->searchInArrayData($parsed, $search);
                $this->log('debug', sprintf('Local dataset %s found', $search->getDataset()), $search->getParameters());
                break;
            }
        }

        return $result;
    }

    /**
     * @param array $parsed
     * @param Search $search
     *
     * @return array
     */
    private function searchInArrayData(array $parsed, Search $search)
    {
        $data = array();
        $query = $search->getQueryValueFilter();
        $columnSearch = $search->getQueryColumnFilter();
        foreach ($parsed as $line) {
            if (empty($query)) {
                $data[] = $line;
            } else {
                if (!empty($columnSearch)) {
                    if (false !== strpos($line[$columnSearch], $query)) {
                        $data[] = $line;
                    }
                } else {
                    foreach ($line as $value) {
                        if (false !== strpos($value, $query)) {
                            $data[] = $line;
                            break;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Look locally (parsers)
     *
     * @param Search $search
     *
     * @return array|false
     */
    private function searchDistant(Search $search)
    {
        $result = $this->client->get('search', $search->getParameters());
        if ($result) {
            $result = json_decode($result, true);
        }

        return $result;
    }

    /**
     * @param Download $download
     * @param bool $forceUpdate
     *
     * @return false|string
     */
    public function download(Download $download, $forceUpdate = false)
    {
        return $this->downloader->download(
            $download->getDataset(),
            $download->getFormat(),
            $download->getFilter(),
            $forceUpdate
        );
    }

    /**
     * @param Download $download
     *
     * @return null|string
     */
    public function getLocalDatasetContent(Download $download)
    {
        $content = null;
        $filepath = $this->finder->findDataset(
            $download->getDataset(),
            $download->getFormat(),
            $download->getFilter()
        );
        if ($filepath) {
            $content = $this->finder->getContent($filepath);
        }

        return $content;
    }
}
