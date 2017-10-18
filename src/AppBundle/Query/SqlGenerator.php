<?php

namespace AppBundle\Query;

class SqlGenerator implements SqlGeneratorInterface
{
    /** @var  QueryBuilderInterface */
    private $queryBuilder;

    /**
     * RestaurantRepository constructor.
     *
     * @param QueryBuilderInterface $queryBuilder
     */
    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Generate sql select query
     *
     * @param string $select
     * @param string $from
     * @param string $where
     *
     * @return string
     */
    public function generateSelectSqlWithWhere(string $select, string $from, string $where) : string
    {
        return $this->queryBuilder
            ->select($select)
            ->from($from)
            ->where($where)
            ->getSQL();
    }
}
