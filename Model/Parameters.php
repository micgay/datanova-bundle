<?php

namespace Laposte\DatanovaBundle\Model;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Florian Ajir <florianajir@gmail.com>
 */
abstract class Parameters extends ParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string $query
     */
    public function getFilter()
    {
        return $this->get('q');
    }

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setFilter($query)
    {
        $this->set('q', $query);

        return $this;
    }

    /**
     * @param $lang
     *
     * @return self
     */
    public function setLang($lang)
    {
        $this->set('lang', $lang);

        return $this;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->get('lang');
    }

    /**
     * Get column prefix of a query
     *
     * @return string
     */
    public function getFilterColumn()
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
    public function getFilterValue()
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
}
