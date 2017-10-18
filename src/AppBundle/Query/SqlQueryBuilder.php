<?php

namespace AppBundle\Query;


class SqlQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array The array of SQL parts collected.
     */
    private $sqlParts = [
        'select'  => [],
        'from'    => [],
        'where'   => null
    ];

    /**
     * Either appends to or replaces a single, generic query part.
     *
     * The available parts are: 'select', 'from', 'where',
     *
     * @param string       $sqlPartName
     * @param string|array $sqlPart
     * @param boolean      $append
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     */
    private function add($sqlPartName, $sqlPart, $append = false) : QueryBuilderInterface
    {
        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = [$sqlPart];
        }

        if ($append) {
            if ($sqlPartName === "select") {
                foreach ($sqlPart as $part) {
                    $this->sqlParts[$sqlPartName][] = $part;
                }
            } elseif ($isArray && is_array($sqlPart[key($sqlPart)])) {
                $key = key($sqlPart);
                $this->sqlParts[$sqlPartName][$key][] = $sqlPart[$key];
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            } else {
                $this->sqlParts[$sqlPartName] = $sqlPart;
            }

            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;

        return $this;
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param mixed $select The selection expressions.
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     */
    public function select($select = null) : QueryBuilderInterface
    {
        if (empty($select)) {
            return $this;
        }

        $selects = is_array($select) ? $select : func_get_args();

        return $this->add('select', $selects, false);
    }

    /**
     * Creates and adds a query root corresponding to the table identified by the
     * given alias, forming a cartesian product with any existing query roots.
     *
     * @param string      $from  The table.
     * @param string|null $alias The alias of the table.
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     */
    public function from($from, $alias = null) : QueryBuilderInterface
    {
        return $this->add('from', [
            'table' => $from,
            'alias' => $alias
        ], true);
    }

    /**
     * Specifies one or more restrictions to the query result.
     * Replaces any previously specified restrictions, if any.
     *
     * @param mixed $predicates The restriction predicates.
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     */
    public function where($predicates) : QueryBuilderInterface
    {
        return $this->add('where', $predicates);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * conjunction with any previously specified restrictions.
     *
     * @param mixed $where The query restrictions.
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     *
     * @see where()
     */
    public function andWhere($where) : QueryBuilderInterface
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        array_unshift($args, $where);
        $where = new CompositeExpression(CompositeExpression::TYPE_AND, $args);

        return $this->add('where', $where, true);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * @param mixed $where The WHERE statement.
     *
     * @return QueryBuilderInterface This QueryBuilder instance.
     *
     * @see where()
     */
    public function orWhere($where) : QueryBuilderInterface
    {
        $args = func_get_args();
        $where = $this->getQueryPart('where');

        array_unshift($args, $where);
        $where = new CompositeExpression(CompositeExpression::TYPE_OR, $args);

        return $this->add('where', $where, true);
    }

    /**
     * Gets a query part by its name.
     *
     * @param string $queryPartName
     *
     * @return mixed
     */
    private function getQueryPart($queryPartName)
    {
        return $this->sqlParts[$queryPartName];
    }

    /**
     * @return string
     */
    public function getSQL() : string
    {
        $query = 'SELECT ' . implode(', ', $this->sqlParts['select']);

        $query .= ($this->sqlParts['from'] ? ' FROM ' . implode(', ', $this->getFromClauses()) : '')
            . $this->getWhereSQL()
            ;

        return $query;
    }

    /**
     * @return string[]
     */
    private function getFromClauses() : array
    {
        $fromClauses = [];

        // Loop through all FROM clauses
        foreach ($this->sqlParts['from'] as $from) {
            if ($from['alias'] === null) {
                $tableSql = $from['table'];
                $tableReference = $from['table'];
            } else {
                $tableSql = $from['table'] . ' ' . $from['alias'];
                $tableReference = $from['alias'];
            }

            $fromClauses[$tableReference] = $tableSql;
        }

        return $fromClauses;
    }

    /**
     * @param string $whereKey
     *
     * @return string
     */
    public function getWhereSQL($whereKey = ' WHERE ') : string
    {
        return $this->sqlParts['where'] !== null ?  $whereKey . ((string)$this->sqlParts['where']) : '';
    }
}
