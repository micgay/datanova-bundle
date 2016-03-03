<?php

namespace LaPoste\DataNovaBundle\Model\Records;

class Search
{
    const ENDPOINT = '/search';

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
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
