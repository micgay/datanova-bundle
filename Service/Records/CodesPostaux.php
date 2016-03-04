<?php

namespace LaPoste\DataNovaBundle\Service\Records;

use LaPoste\DataNovaBundle\Model\Records\Search;
use LaPoste\DataNovaBundle\Provider\Records;

class CodesPostaux
{
    /** Dataset */
    const DATASET_ID = 'laposte_hexasmal';

    /** City field */
    const FIELD_CITY_NAME = 'nom_de_la_commune';

    /** Postal code field */
    const FIELD_POSTAL_CODE = 'code_postal';

    /** Mode to return associative array on search */
    const ASSOCIATIVE_MODE = 0;

    /** Mode to return indexed array on search */
    const LIST_MODE = 1;

    /** @var Records */
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
    public function search($query = null, $start = null, $rows = null)
    {
        $result = array();
        $parameters = $this->buildParameters($query, $start, $rows);
        $search = new Search(self::DATASET_ID, $parameters);
        $response = $this->provider->get($search::ENDPOINT, $search->getParameters());
        if (isset($response)) {
            $result = json_decode($response, true);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param int $start
     * @param int $rows
     *
     * @return array
     */
    private function buildParameters($query, $start, $rows)
    {
        $parameters = array();
        $parameters['sort'] = self::FIELD_POSTAL_CODE;
        if (isset($query)) {
            $parameters['q'] = $query;
        }
        if (isset($start)) {
            $parameters['start'] = $start;
        }
        if (isset($rows)) {
            $parameters['rows'] = $rows;
        }

        return $parameters;
    }

    /**
     * @param array $response
     * @param string $keyField
     * @param string $valueField
     *
     * @return array
     */
    public static function associativeArrayResults(
        array $response,
        $keyField = self::FIELD_POSTAL_CODE,
        $valueField = self::FIELD_CITY_NAME
    ) {
        $parsed = array();
        foreach ($response['records'] as $record) {
            $key = $record['fields'][$keyField];
            $value = $record['fields'][$valueField];
            $parsed[$key] = $value;
        }

        return $parsed;
    }

    /**
     * @param array $response
     * @param string $facetName
     *
     * @return array
     */
    public static function listResults(array $response, $facetName = self::FIELD_CITY_NAME)
    {
        $parsed = array();
        foreach ($response['records'] as $record) {
            $value = $record['fields'][$facetName];
            if (false === array_search($value, $parsed)) {
                $parsed[] = $value;
            }
        }

        return $parsed;
    }
}
