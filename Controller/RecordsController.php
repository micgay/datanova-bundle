<?php

namespace LaPoste\DataNovaBundle\Controller;

use LaPoste\DataNovaBundle\Service\Records\CodesPostaux;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function codesPostauxAction(Request $request)
    {
        $query = $request->request->get('q');
        $start = $request->request->get('start');
        $rows = $request->request->get('rows');
        $result = $this
            ->getCodesPosteauxRecordsService()
            ->search($query, $start, $rows);

        return new Response(json_encode($result));
    }

    /**
     * @return CodesPostaux
     */
    private function getCodesPosteauxRecordsService()
    {
        return $this->get('data_nova.service.records.codes_postaux');
    }
}
