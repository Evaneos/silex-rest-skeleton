<?php

namespace Evaneos\REST\API\Converters;

use Symfony\Component\HttpFoundation\Request;

class PaginationConverter
{
    /**
     * @var int
     */
    private $defaultLimit;

    /**
     * @var int
     */
    private $maxLimit;

    /**
     * Constructor.
     * 
     * @param int $defaultLimit
     * @param int $maxLimit
     */
    public function __construct($defaultLimit, $maxLimit)
    {
        $this->defaultLimit = $defaultLimit;
        $this->maxLimit = $maxLimit;
    }

    /**
     * @param int     $page
     * @param Request $request
     *
     * @return int
     */
    public function convertPage($page, Request $request)
    {
        $page = intval($request->query->get('page'));

        return $page > 1 ? $page : 1;
    }

    /**
     * @param int     $limit
     * @param Request $request
     *
     * @return int
     */
    public function convertLimit($limit, Request $request)
    {
        $limit = intval($request->query->get('limit',  $this->defaultLimit));

        $limit = $limit > 0 ? $limit : $this->defaultLimit;

        return $limit < $this->maxLimit ? $limit : $this->maxLimit;
    }
}
