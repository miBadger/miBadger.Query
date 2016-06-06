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
 * The query result test class.
 *
 * @since 1.0.0
 */
class QueryResultTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->pdo = new \PDO('sqlite::memory:');

		$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$this->pdo->query('CREATE TABLE IF NOT EXISTS `test` (`id` INTEGER PRIMARY KEY, `name` VARCHAR(255))');
		$this->pdo->query('INSERT INTO `test` (`id`, `name`) VALUES (1, "John Doe")');
		$this->pdo->query('INSERT INTO `test` (`id`, `name`) VALUES (2, "Jane Doe")');
	}

	public function testIterator()
	{
		$queryResult = (new Query($this->pdo, 'test'))
			->select()
			->execute();

		$result = [];

		foreach ($queryResult as $row) {
			$result[] = $row['id'];
		}

		$this->assertEquals(['1', '2'], $result);
	}

	public function testFetch()
	{
		$queryResult = (new Query($this->pdo, 'test'))
			->select()
			->execute();

		$this->assertEquals(['id' => '1', 'name' => 'John Doe'], $queryResult->fetch());
	}

	public function testFetchAll()
	{
		$queryResult = (new Query($this->pdo, 'test'))
			->select()
			->execute();

		$this->assertEquals([
			['id' => '1', 'name' => 'John Doe'],
			['id' => '2', 'name' => 'Jane Doe']
		], $queryResult->fetchAll());
	}
}
