<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace miBadger\Query;

/**
 * The query test class.
 *
 * @since 1.0.0
 */
class QueryTest extends \PHPUnit_Framework_TestCase
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

	public function testSelectWhere()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('name', 'LIKE', 'John Doe');

		$this->assertEquals('SELECT * FROM test WHERE name LIKE :name', (string) $query);
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

		$this->assertEquals('SELECT * FROM test LIMIT :limit', (string) $query);
	}

	public function testSelectOffset()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->limit(1)
			->offset(1);

		$this->assertEquals('SELECT * FROM test LIMIT :limit OFFSET :offset', (string) $query);
	}

	public function testInsert()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->insert(['name' => 'John Doe']);

		$this->assertEquals('INSERT INTO test (name) VALUES (:name)', (string) $query);
	}

	public function testUpdate()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->update(['name' => 'John Doe'])
			->where('id', '=', 1);

		$this->assertEquals('UPDATE test SET name = :name WHERE id = :id', (string) $query);
	}

	public function testDelete()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->delete()
			->where('id', '=', 1);

		$this->assertEquals('DELETE FROM test WHERE id = :id', (string) $query);
	}

	public function testExecute()
	{
		$pdo = new \PDO('sqlite::memory:');
		$pdo->query('CREATE TABLE IF NOT EXISTS `test` (`id` INTEGER PRIMARY KEY, `name` VARCHAR(255))');
		$pdo->query('INSERT INTO `test` (`id`, `name`) VALUES (1, "John Doe")');

		$query = (new Query($pdo, 'test'))
			->select()
			->where('id', '=', 1);

		$result = $query->execute()->fetch();

		$this->assertEquals('1', $result['id']);
		$this->assertEquals('John Doe', $result['name']);
	}
}
