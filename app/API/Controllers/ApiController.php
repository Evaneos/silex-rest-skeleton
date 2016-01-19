<?php

namespace Evaneos\REST\API\Controllers;

use Evaneos\REST\API\Converters\ApiResponseBuilder;
use Evaneos\REST\API\Resources\Root;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    /**
     * @var ApiResponseBuilder
     */
    private $responseBuilder;

    /**
     * Constructor.
     *
     * @param ApiResponseBuilder $responseBuilder
     */
    public function __construct(ApiResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * Route root.
     *
     * @return Response
     */
    public function root()
    {
        return $this->responseBuilder->buildResponse(new Response(), new Root());
    }
}
