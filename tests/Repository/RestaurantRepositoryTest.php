<?php

namespace Tests\Repository;

use AppBundle\Query\MongoSqlWhereConverter;
use AppBundle\Query\SqlGenerator;
use AppBundle\Query\SqlQueryBuilder;
use AppBundle\Repository\RestaurantRepository;

class RestaurantRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  RestaurantRepository */
    private $restaurantRepository;

    public function setUp()
    {
        parent::setUp();

        $this->restaurantRepository = new RestaurantRepository(
            new SqlGenerator(new SqlQueryBuilder()),
            'restaurants',
            new MongoSqlWhereConverter(new SqlQueryBuilder())
        );
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->restaurantRepository);
    }

    /**
     * @dataProvider findProvider
     */
    public function testFind(array $parameters, string $expected)
    {
        $this->assertEquals($this->restaurantRepository->find($parameters), $expected);
    }

    public function findProvider()
    {
        return [
            [["borough" => "Manhattan"], "SELECT * FROM restaurants WHERE borough='Manhattan'"],
            [["grades_score" => ['$gt' => 30]], "SELECT * FROM restaurants WHERE grades_score>30"],
            [["grades_score" => ['$lt' => 30]], "SELECT * FROM restaurants WHERE grades_score<30"],
            [["cuisine" => "Italian", "zipcode" => "10075"], "SELECT * FROM restaurants WHERE (cuisine='Italian') AND (zipcode='10075')"],
            [['$or' => ["cuisine" => "Italian", "zipcode" => "10075"]], "SELECT * FROM restaurants WHERE (cuisine='Italian') OR (zipcode='10075')"],
            [['$or' => ["cuisine" => ['$lt' => 30], "zipcode" => "10075"]], "SELECT * FROM restaurants WHERE (cuisine<30) OR (zipcode='10075')"],
            [['$or' => ["cuisine" => ['$lt' => 30], "zipcode" => "10075"], "grades_score" => 30], "SELECT * FROM restaurants WHERE ((cuisine<30) OR (zipcode='10075')) AND (grades_score=30)"],
            [["grades_score" => 30, '$or' => ["cuisine" => ['$lt' => 30], "zipcode" => "10075"]], "SELECT * FROM restaurants WHERE (grades_score=30) AND ((cuisine<30) OR (zipcode='10075'))"]
        ];
    }
}
