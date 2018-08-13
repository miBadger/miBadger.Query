<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */

namespace miBadger\Query;

use PHPUnit\Framework\TestCase;

/**
 * The query test class.
 *
 * @since 1.0.0
 */
class QueryTest extends TestCase
{
	public function test__toString()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'));

		$this->assertEquals('', (string) $query);
	}

	public function testSelect()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select();

		$this->assertEquals('SELECT * FROM test', (string) $query);
	}

	public function testSelectJoin()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->join('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test INNER JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectLeftJoin()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->leftJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test LEFT JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectRightJoin()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->rightJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test RIGHT JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectCrossJoin()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->crossJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test CROSS JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectWhere()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('name', 'LIKE', 'John Doe');

		$this->assertEquals('SELECT * FROM test WHERE name LIKE :where1', (string) $query);
	}

	public function testSelectWhereKeyDuplicate()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('name', 'LIKE', '%John%')
			->where('name', 'NOT LIKE', '%Doe%');

		$this->assertEquals('SELECT * FROM test WHERE name LIKE :where1 AND name NOT LIKE :where2', (string) $query);
	}

	public function testSelectWhereKeyFunction()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('LOWER(name)', 'LIKE', 'john doe');

		$this->assertEquals('SELECT * FROM test WHERE LOWER(name) LIKE :where1', (string) $query);
	}

	public function testSelectWhereOperatorIn()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('name', 'IN', ['John Doe', 'Jane Doe']);

		$this->assertEquals('SELECT * FROM test WHERE name IN (:where1, :where2)', (string) $query);
	}

	public function testSelectGroupBy()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->groupBy('name');

		$this->assertEquals('SELECT * FROM test GROUP BY name', (string) $query);
	}

	public function testSelectOrderBy()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->orderBy('name', 'desc');

		$this->assertEquals('SELECT * FROM test ORDER BY name DESC', (string) $query);
	}

	public function testSelectLimit()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->limit(1);

		$this->assertEquals('SELECT * FROM test LIMIT :limit1', (string) $query);
	}

	public function testSelectOffset()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->limit(1)
			->offset(1);

		$this->assertEquals('SELECT * FROM test LIMIT :limit1 OFFSET :offset1', (string) $query);
	}

	public function testInsert()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->insert(['name' => 'John Doe']);

		$this->assertEquals('INSERT INTO test (name) VALUES (:insert1)', (string) $query);
	}

	public function testUpdate()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->update(['name' => 'John Doe'])
			->where('id', '=', 1);

		$this->assertEquals('UPDATE test SET name = :update1 WHERE id = :where1', (string) $query);
	}

	public function testDelete()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->delete()
			->where('id', '=', 1);

		$this->assertEquals('DELETE FROM test WHERE id = :where1', (string) $query);
	}

	public function testExecute()
	{
		$pdo = new \PDO('sqlite::memory:');
		$pdo->query('CREATE TABLE IF NOT EXISTS `test` (`id` INTEGER PRIMARY KEY, `name` VARCHAR(255), `active` BIT)');
		$pdo->query('INSERT INTO `test` (`id`, `name`, `active`) VALUES (1, "John Doe", 1)');

		$query = (new Query($pdo, 'test'))
			->select()
			->where('id', '=', 1)
			->where('name', 'LIKE', 'John Doe')
			->where('name', 'IS NOT', null)
			->where('active', '=', true);

		$result = $query->execute()->fetch();

		$this->assertEquals('1', $result['id']);
		$this->assertEquals('John Doe', $result['name']);
	}
}
