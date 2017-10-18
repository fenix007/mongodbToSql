<?php

namespace AppBundle\Repository;

use AppBundle\Query\MongoSqlWhereConverter;
use AppBundle\Query\SqlGeneratorInterface;

class RestaurantRepository
{
    /** @var string */
    private $tableName;

    /** @var  SqlGeneratorInterface */
    private $sqlGenerator;

    /** @var  MongoSqlWhereConverter */
    private $mongoSqlWhereConverter;

    /**
     * RestaurantRepository constructor.
     *
     * @param SqlGeneratorInterface  $sqlGenerator
     * @param string                 $tableName
     *
     * @param MongoSqlWhereConverter $mongoSqlWhereConverter
     *
     * @internal param QueryBuilderInterface $queryBuilder
     */
    public function __construct(SqlGeneratorInterface $sqlGenerator, string $tableName, MongoSqlWhereConverter $mongoSqlWhereConverter)
    {
        $this->sqlGenerator = $sqlGenerator;
        $this->tableName    = $tableName;
        $this->mongoSqlWhereConverter = $mongoSqlWhereConverter;
    }

    public function find(array $parameters) : string
    {
        return $this->sqlGenerator->generateSelectSqlWithWhere(
            '*',
            $this->tableName,
            $this->mongoSqlWhereConverter->convert($parameters)
        );
    }
}
