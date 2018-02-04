<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */

namespace miBadger\Query;

/**
 * The query result class.
 *
 * @since 1.0.0
 */
class QueryResult implements \IteratorAggregate
{
	/** @var \PDOStatement The PDO statement. */
	private $pdoStatement;

	/**
	 * Construct a query result object with the given pdo statement.
	 *
	 * @param \PDOStatement $pdoStatement
	 */
	public function __construct(\PDOStatement $pdoStatement)
	{
		$this->pdoStatement = $pdoStatement;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return $this->pdoStatement;
	}

	/**
	 * Fetches a row from the result set.
	 *
	 * @return mixed a row from the result set.
	 */
	public function fetch()
	{
		return $this->pdoStatement->fetch();
	}

	/**
	 * Fetches all rows from the result set.
	 *
	 * @return array all rows from the result set.
	 */
	public function fetchAll()
	{
		return $this->pdoStatement->fetchAll();
	}
}
