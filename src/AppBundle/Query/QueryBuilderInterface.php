<?php

namespace AppBundle\Query;

interface QueryBuilderInterface
{
    public function getSQL() : string;
    public function select($select = null) : QueryBuilderInterface;
    public function from($from, $alias = null) : QueryBuilderInterface;
    public function where($where) : QueryBuilderInterface;
    public function andWhere($where) : QueryBuilderInterface;
    public function orWhere($where) : QueryBuilderInterface;
    public function getWhereSQL($whereKey = ' WHERE ') : string;
}
