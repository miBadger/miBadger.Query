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
 * The query builder test class.
 *
 * @since 1.0.0
 */
class QueryBuilderTest extends TestCase
{
	public function test__toString()
	{
		$query = (new QueryBuilder('test'));

		$this->assertEquals('', (string) $query);
	}

	public function testSelect()
	{
		$query = (new QueryBuilder('test'))
			->select();

		$this->assertEquals('SELECT * FROM test', (string) $query);
	}

	public function testSelectColumn()
	{
		$query = (new QueryBuilder('test'))
			->select(['name']);

		$this->assertEquals('SELECT name FROM test', (string) $query);
	}

	public function testSelectJoin()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->join('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test INNER JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectLeftJoin()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->leftJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test LEFT JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectRightJoin()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->rightJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test RIGHT JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectCrossJoin()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->crossJoin('test2', 'test.id', '=', 'test2.id');

		$this->assertEquals('SELECT * FROM test CROSS JOIN test2 ON test.id = test2.id', (string) $query);
	}

	public function testSelectWhere()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->where(Query::Like('name', 'John Doe'));

		$this->assertEquals('SELECT * FROM test WHERE name LIKE John Doe', (string) $query);
	}

	public function testSelectWhereMultiple()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->where(Query::And(
					Query::Like('name', 'John Doe'),
					Query::Like('email', 'john@doe.com')
				));

		$this->assertEquals('SELECT * FROM test WHERE ( name LIKE John Doe ) AND ( email LIKE john@doe.com )', (string) $query);
	}

	public function testSeelectWhereIn()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->where(Query::In('name', 'John Doe, Jane Doe'));

		$this->assertEquals('SELECT * FROM test WHERE name IN (John Doe, Jane Doe)', (string) $query);
	}

	public function testSeelectWhereInArray()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->where(Query::In('name', ['John Doe', 'Jane Doe']));

		$this->assertEquals('SELECT * FROM test WHERE name IN (John Doe, Jane Doe)', (string) $query);
	}

	public function testSelectGroupBy()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->groupBy('name');

		$this->assertEquals('SELECT * FROM test GROUP BY name', (string) $query);
	}

	public function testSelectOrderBy()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->orderBy('name');

		$this->assertEquals('SELECT * FROM test ORDER BY name', (string) $query);
	}

	public function testSelectOrderByAsc()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->orderBy('name', 'asc');

		$this->assertEquals('SELECT * FROM test ORDER BY name ASC', (string) $query);
	}

	public function testSelectOrderByDesc()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->orderBy('name', 'desc');

		$this->assertEquals('SELECT * FROM test ORDER BY name DESC', (string) $query);
	}

	public function testSelectLimit()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->limit(1);

		$this->assertEquals('SELECT * FROM test LIMIT 1', (string) $query);
	}

	public function testSelectOffset()
	{
		$query = (new QueryBuilder('test'))
			->select()
			->limit(1)
			->offset(1);

		$this->assertEquals('SELECT * FROM test LIMIT 1 OFFSET 1', (string) $query);
	}

	public function testInsert()
	{
		$query = (new QueryBuilder('test'))
			->insert(['name' => 'John Doe']);

		$this->assertEquals('INSERT INTO test (name) VALUES (John Doe)', (string) $query);
	}

	public function testInsertMultiple()
	{
		$query = (new QueryBuilder('test'))
			->insert(['name' => 'John Doe', 'email' => 'john@doe.com']);

		$this->assertEquals('INSERT INTO test (name, email) VALUES (John Doe, john@doe.com)', (string) $query);
	}

	public function testUpdate()
	{
		$query = (new QueryBuilder('test'))
			->update(['name' => 'John Doe'])
			->where(Query::Equal('id', 1));

		$this->assertEquals('UPDATE test SET name = John Doe WHERE id = 1', (string) $query);
	}

	public function testUpdateMultiple()
	{
		$query = (new QueryBuilder('test'))
			->update(['name' => 'John Doe', 'email' => 'john@doe.com'])
			->where(Query::Equal('id', 1));

		$this->assertEquals('UPDATE test SET name = John Doe, email = john@doe.com WHERE id = 1', (string) $query);
	}

	public function testUpdateLimit()
	{
		$query = (new QueryBuilder('test'))
			->update(['name' => 'John Doe'])
			->where(Query::Equal('id', 1))
			->limit(1);

		$this->assertEquals('UPDATE test SET name = John Doe WHERE id = 1 LIMIT 1', (string) $query);
	}

	public function testDelete()
	{
		$query = (new QueryBuilder('test'))
			->delete()
			->where(Query::Equal('id', 1));

		$this->assertEquals('DELETE FROM test WHERE id = 1', (string) $query);
	}

	public function testDeleteLimit()
	{
		$query = (new QueryBuilder('test'))
			->delete()
			->limit(1);

		$this->assertEquals('DELETE FROM test LIMIT 1', (string) $query);
	}
}
