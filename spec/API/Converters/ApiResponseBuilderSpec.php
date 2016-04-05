<?php

namespace spec\Evaneos\REST\API\Converters;

use Hateoas\Hateoas;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

class ApiResponseBuilderSpec extends ObjectBehavior
{
    function let(SerializerInterface $serializer)
    {
        $this->beConstructedWith($serializer);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\API\Converters\ApiResponseBuilder');
    }

    function it_uses_the_serilizer_in_order_to_build_a_response(SerializerInterface $serializer)
    {
        $response = new Response('', 123);
        $serializer->serialize('pony resource', 'json')->willReturn('A json pony');
        $this->buildResponse($response, 'pony resource')->shouldBeLike(new Response('A json pony', 123, [
            'Content-type' => 'application/json'
        ]));
    }
}
