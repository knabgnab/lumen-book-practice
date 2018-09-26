<?php

namespace App\Http\Response;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;
use Illuminate\Http\Request;

use Log;

class FractalResponse
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var SerializerAbstract
     */
    private $serializer;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Manager $manager, SerializerAbstract $serializer, Request $request)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->manager->setSerializer($serializer);
        $this->request = $request;
    }

    /**
     * Get the includes from the request if none are passed.
     *
    * @param null $includes
    */
    public function parseIncludes($includes = null)
    {
        if (empty($includes)) {
            $includes = $this->request->query('include', '');
            Log::info(print_r($includes, true));
        }

        $this->manager->parseIncludes($includes);
    }

    public function item($data, TransformerAbstract $transformer, $resourceKey = null)
    {
        $resource = new Item($data, $transformer, $resourceKey);

        return $this->manager->createData($resource)->toArray();
    }

    public function collection($data, TransformerAbstract $transformer, $resourceKey =null)
    {
        $resource = new Collection($data, $transformer, $resourceKey);
        return $this->manager->createData($resource)->toArray();
    }

    private function createDataArray(ResourceInterface $resource)
    {
        return $this->manager->createData($resource)->toArray();
    }
}
