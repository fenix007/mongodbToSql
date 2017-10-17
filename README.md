Разработайте адаптер который принимает похожий на mongodb синтаксис и конвертирует его в SQL

Implement adapter that accepts mongodb-like syntax and converts it into sql.
Покройте ваш код тестами.
Работать с базой данных не нужно, достаточно вывода в виде простого sql.
Не используйте Doctrine или другие ORM зависимости для этой задачи.
В вашем коде, пожалуйста, используйте PSR стандарты, композер, symfony 3 фреймворк, php 7.1+.
Код должен соответсовать принципам SOLID.

Больше примеров: https://docs.mongodb.com/getting-started/shell/query/
Ограничьте вашу реализацию примерами данными ниже, можно предусмотреть их комбинации.

```INPUT:
$repo->find(["borough" => "Manhattan"])
OUTPUT:
select * from restaurants where borough='Manhattan';

INPUT:
$repo->find(["grades_score" => ['$gt' => 30]])
OUTPUT:
select * from restaurants where grades_score > 30;

INPUT:
$repo->find(["grades_score" => ['$lt' => 30]])
OUTPUT:
select * from restaurants where grades_score < 30;

INPUT:
$repo->find(["cuisine" => "Italian", "zipcode" => "10075"])
OUTPUT:
select * from restaurants where cuisine='Italian' and zipcode='10075';

INPUT:
$repo->find(["$or" => ["cuisine" => "Italian", "zipcode" => "10075"]])
OUTPUT:
select * from restaurants where cuisine='Italian' or zipcode='10075';
```