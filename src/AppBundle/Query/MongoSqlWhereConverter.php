<?php

namespace AppBundle\Query;

class MongoSqlWhereConverter
{
    const OR_KEY = '$or';
    const GT_KEY = '$gt';
    const LT_KEY = '$lt';

    const SQL_AND_KEY = 'AND';
    const SQL_OR_KEY = 'OR';
    const SQL_EQ_KEY = '=';
    const SQL_GT_KEY = '>';
    const SQL_LT_KEY = '<';

    /** @var  QueryBuilderInterface */
    protected $queryBuilder;

    /**
     * MongoSqlWhereConverter constructor.
     *
     * @param QueryBuilderInterface $queryBuilder
     */
    public function __construct(QueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param array  $parameters
     *
     * @param string $key
     *
     * @return string
     */
    public function convert(array $parameters, $key = self::SQL_AND_KEY) : string
    {
        $queryBuilder = clone $this->queryBuilder;

        foreach ($parameters as $fieldOrKey => $parameter) {
            if ($fieldOrKey === self::OR_KEY) {
                $queryBuilder->andWhere($this->convert($parameter, self::SQL_OR_KEY));
                continue;
            }

            if ($key === self::SQL_OR_KEY) {
                $queryBuilder->orWhere($fieldOrKey . $this->checkCompareSql($parameter));
            } else {
                $queryBuilder->andWhere($fieldOrKey . $this->checkCompareSql($parameter));
            }
        }

        return $queryBuilder->getWhereSQL('');
    }

    /**
     * @param array|string|int $parameters
     *
     * @return string
     */
    private function checkCompareSql($parameters) : string
    {
        if (!is_array($parameters)) {
            //TODO: escape
            return $this->addConditionSql('', is_string($parameters) ? "'$parameters'": $parameters);
        }

        if (count($parameters) === 1) {
            $parameter = reset($parameters);
            $fieldOrKey = key($parameters);

            return $this->addConditionSql($fieldOrKey, $parameter);
        }

        return $this->convert($parameters);
    }

    /**
     * @param string $fieldOrKey
     * @param        $parameter
     *
     * @return string
     */
    private function addConditionSql($fieldOrKey, $parameter) : string
    {
        switch ($fieldOrKey) {
            case self::GT_KEY:
                return self::SQL_GT_KEY . $parameter;
            case self::LT_KEY:
                return self::SQL_LT_KEY . $parameter;
            default:
                return $fieldOrKey . self::SQL_EQ_KEY . $parameter;
        }
    }
}
