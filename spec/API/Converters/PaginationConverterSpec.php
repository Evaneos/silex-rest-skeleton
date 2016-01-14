<?php

namespace spec\Evaneos\REST\API\Converters;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class PaginationConverterSpec extends ObjectBehavior
{
    const DEFAULT_LIMIT = 20;

    const MAX_LIMIT = 50;

    function let()
    {
        $this->beConstructedWith(self::DEFAULT_LIMIT, self::MAX_LIMIT);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\REST\API\Converters\PaginationConverter');
    }
    
    function it_returns_page_contained_in_request()
    {
        $request = new Request();
        $request->query->set('page', '55');
        $this->convertPage('something useless', $request)->shouldReturn(55);
    }

    function it_returns_1_if_page_is_not_a_numeric()
    {
        $request = new Request();
        $request->query->set('page', 'aaa');
        $this->convertPage('something useless', $request)->shouldReturn(1);
    }
    
    function it_returns_1_if_page_is_below_or_equal_to_0()
    {
        $request = new Request();
        $request->query->set('page', '-5');
        $this->convertPage('something useless', $request)->shouldReturn(1);

        $request = new Request();
        $request->query->set('page', '0');
        $this->convertPage('something useless', $request)->shouldReturn(1);
    }

    function it_returns_1_if_no_page_in_query()
    {
        $request = new Request();
        $this->convertPage('something useless', $request)->shouldReturn(1);
    }
    
    function it_returns_default_limit_if_no_limit_in_query()
    {
        $request = new Request();
        $this->convertLimit('something useless', $request)->shouldReturn(self::DEFAULT_LIMIT);
    }
    
    function it_returns_limit_if_in_query()
    {
        $request = new Request();
        $request->query->set('limit', 33);
        $this->convertLimit('something useless', $request)->shouldReturn(33);
    }

    function it_returns_default_limit_if_limit_is_not_numeric()
    {
        $request = new Request();
        $request->query->set('limit', 'aaa');
        $this->convertLimit('something useless', $request)->shouldReturn(self::DEFAULT_LIMIT);
    }

    function it_returns_default_limit_if_queried_limit_is_0_or_under()
    {
        $request = new Request();
        $request->query->set('limit', '0');
        $this->convertLimit('something useless', $request)->shouldReturn(self::DEFAULT_LIMIT);

        $request = new Request();
        $request->query->set('limit', '-12');
        $this->convertLimit('something useless', $request)->shouldReturn(self::DEFAULT_LIMIT);
    }

    function it_returns_max_limit_if_queried_limit_is_above()
    {
        $request = new Request();
        $request->query->set('limit', self::MAX_LIMIT + 12);
        $this->convertLimit('something useless', $request)->shouldReturn(self::MAX_LIMIT);

        $request = new Request();
        $request->query->set('limit', self::MAX_LIMIT);
        $this->convertLimit('something useless', $request)->shouldReturn(self::MAX_LIMIT);
    }
}
