<?php

namespace LaPoste\DataNovaBundle\Controller;

use LaPoste\DataNovaBundle\Service\Records\CodesPostaux;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostalCodesController extends Controller
{
    /**
     * @param string $query
     * @param int $start
     * @param int $rows
     *
     * @return Response
     */
    public function searchAssociativeAction($query, $start, $rows)
    {
        $results = $this->searchInLaposteHexasmalDataset($query, $start, $rows);
        $results = CodesPostaux::associativeArrayResults($results);

        return new Response(json_encode($results));
    }

    /**
     * @param string $query
     * @param int $start
     * @param int $rows
     *
     * @return Response
     */
    public function listCitiesAction($query, $start, $rows)
    {
        $results = $this->searchInLaposteHexasmalDataset($query, $start, $rows);
        $results = CodesPostaux::listResults($results);

        return new Response(json_encode($results));
    }

    /**
     * @param string $query
     * @param int $start
     * @param int $rows
     *
     * @return array
     */
    private function searchInLaposteHexasmalDataset($query, $start, $rows)
    {
        return $this
            ->get('data_nova.service.records.codes_postaux')
            ->search($query, $start, $rows)
        ;
    }
}
