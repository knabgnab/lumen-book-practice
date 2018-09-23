<?php

namespace App\Http\Controllers;

use App\Http\Response\FractalResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\TransformerAbstract;

use Log;

class Controller extends BaseController
{
    /** Chapter7 - Integrating the Fractal Response Service
     * @var FractalResponse
     */
    private $fractal;

    public function __construct(FractalResponse $fractal)
    {
        $this->fractal = $fractal;
    }

    /**
    * @param $data
    * @param TransformerAbstract $transformer
    * @param null $resourceKey
    * @return array
    */
    public function item($data, TransformerAbstract $transformer, $resourceKey = null)
    {
        return $this->fractal->item($data, $transformer, $resourceKey);
    }

    /**
    * @param $data
    * @param TransformerAbstract $transformer
    * @param null $resourceKey
    * @return array
    */
    public function collection($data, TransformerAbstract $transformer, $resourceKey = null)
    {
        Log::info("Controller");
        return $this->fractal->collection($data, $transformer, $resourceKey);
    }
}
