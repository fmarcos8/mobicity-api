<?php

namespace MobiCity\Controllers;

use MobiCity\Helpers\Log;
use MobiCity\Core\Response;
use MobiCity\Services\SpTrans\SpTransService;
use MobiCity\Services\SpTrans\Transformers\LineTransformer;
use MobiCity\Services\SpTrans\Transformers\VehicleTransformer;

class BusController
{
    public $service;

    public function __construct()
    {
        $this->service = new SpTransService();
    }

    public function searchLines($params)
    {   
        $data = $this->service->getLines($params->line);
        $dataTransformed = LineTransformer::transformCollection($data);
        return Response::json($dataTransformed);        
    }

    public function searchLinePosition($params)
    {
        $data = $this->service->getLinePosition($params->codeLine);
        $dataTransformed = VehicleTransformer::transformPositionLine($data);
        return Response::json($dataTransformed);
    }
}