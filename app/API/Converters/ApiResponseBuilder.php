<?php

namespace Evaneos\REST\API\Converters;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseBuilder
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Build a response.
     *
     * @param Response $response
     * @param mixed    $resource
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function buildResponse(Response $response, $resource)
    {
        $response->setContent($this->serializer->serialize($resource, 'json'));
        $response->headers->set('Content-type', 'application/json');

        return $response;
    }
}
