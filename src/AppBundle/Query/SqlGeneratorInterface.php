<?php

namespace AppBundle\Query;

interface SqlGeneratorInterface
{
    public function generateSelectSqlWithWhere(string $select, string $from, string $where) : string;
}
