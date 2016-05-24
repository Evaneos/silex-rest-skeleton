<?php

namespace Evaneos\REST\DoctrineAdapters;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class DoctrineORMMapperAdapter extends DoctrineORMAdapter
{
    /** @var callable */
    protected $modifier;

    /**
     * Constructor.
     *
     * @param QueryBuilder|Query $query
     * @param callable           $modifier
     * @param bool               $fetchJoinCollection
     * @param bool               $useOutputWalkers
     */
    public function __construct($query, callable $modifier, $fetchJoinCollection = null, $useOutputWalkers = null)
    {
        $this->modifier = $modifier;
        parent::__construct($query, $fetchJoinCollection, $useOutputWalkers);
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Pagerfanta\Adapter\DoctrineORMAdapter::getSlice()
     */
    public function getSlice($offset, $length)
    {
        $results = parent::getSlice($offset, $length);

        return new \ArrayIterator(
            array_map($this->modifier, iterator_to_array($results))
        );
    }
}
