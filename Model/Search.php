<?php

namespace Laposte\DatanovaBundle\Model;

class Search
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $dataset (mandatory) datasetid
     * @param array $parameters if empty, all dataset records will be returned
     */
    public function __construct($dataset, $parameters = array())
    {
        $this->parameters = $parameters;
        $this->parameters['dataset'] = $dataset;
    }

    /**
     * @return string
     */
    public function getDataset()
    {
        return $this->parameters['dataset'];
    }

    /**
     * @param string $query
     *
     * @return self
     */
    public function setQuery($query)
    {
        $this->parameters['q'] = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->parameters['q'];
    }

    /**
     * @return string
     */
    public function getQueryColumnFilter()
    {
        $columnFilter = null;
        $query = $this->parameters['q'];
        if (false !== strpos($query, ':')) {
            $explode = explode(':', $query);
        } elseif (false !== strpos($query, '=')) {
            $explode = explode('=', $query);
        }
        if (isset($explode[0])) {
            $columnFilter = $explode[0];
        }

        return $columnFilter;
    }

    /**
     * @return string
     */
    public function getQueryValueFilter()
    {
        $valueFilter = $query = $this->parameters['q'];
        if (false !== strpos($query, ':')) {
            $explode = explode(':', $query);
        } elseif (false !== strpos($query, '=')) {
            $explode = explode('=', $query);
        }
        if (isset($explode[1])) {
            $valueFilter = $explode[1];
        }

        return $valueFilter;
    }

    /**
     * @param int $start
     *
     * @return self
     */
    public function setStart($start)
    {
        $this->parameters['start'] = $start;

        return $this;
    }

    /**
     * @param int $rows
     *
     * @return self
     */
    public function setRows($rows)
    {
        $this->parameters['rows'] = $rows;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
