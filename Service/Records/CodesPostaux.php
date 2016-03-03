<?php

namespace LaPoste\DataNovaBundle\Service\Records;

use LaPoste\DataNovaBundle\Model\Records\Search;
use LaPoste\DataNovaBundle\Provider\Records;

class CodesPostaux
{
    const DATASET_ID = 'laposte_hexasmal';

    /**
     * @var Records
     */
    private $provider;

    /**
     * @param Records $provider
     */
    public function __construct(Records $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $query
     * @param int $start
     * @param int $rows
     *
     * @return array
     */
    public function search($query = null, $start = 0, $rows = 10)
    {
        $parameters = array();
        if (isset($query)) {
            $parameters['q'] = $query;
        }
        if (isset($start)) {
            $parameters['start'] = $start;
        }
        if (isset($rows)) {
            $parameters['rows'] = $rows;
        }
        $search = new Search(self::DATASET_ID, $parameters);
        $response = $this->provider->get($search::ENDPOINT, $search->getParameters());
        $response = json_decode($response, true);

        return $this->parse($response);
    }

    /**
     * @param array $response
     *
     * @return array
     */
    private function parse(array $response)
    {
        $parsed = array();
        foreach ($response['records'] as $record) {
            $parsed[$record['fields']['code_postal']] = $record['fields']['nom_de_la_commune'];
        }

        return $parsed;
    }
}
