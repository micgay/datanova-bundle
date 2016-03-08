<?php

namespace Laposte\DatanovaBundle\Model;

class Download
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $dataset (mandatory) datasetid
     * @param string $format
     */
    public function __construct($dataset, $format)
    {
        $this->parameters = array();
        $this->parameters['dataset'] = $dataset;
        $this->parameters['format'] = $format;
    }

    /**
     * @return string
     */
    public function getDataset()
    {
        return $this->parameters['dataset'];
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->parameters['format'];
    }

    /**
     * @return string $query
     */
    public function getFilter()
    {
        return $this->parameters['q'];
    }


    /**
     * @param string $query
     */
    public function setFilter($query)
    {
        $this->parameters['q'] = $query;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
