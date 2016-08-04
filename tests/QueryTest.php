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

		$this->assertEquals('SELECT * FROM test WHERE name LIKE :where1', (string) $query);
	}

	public function testSelectWhereNotLike()
	{
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where('name', 'NOT LIKE', 'John Doe');

		$this->assertEquals('SELECT * FROM test WHERE name NOT LIKE :where1', (string) $query);
	}

	public function testSelectWhereIn()
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
