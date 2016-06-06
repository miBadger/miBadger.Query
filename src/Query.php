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
 * The query class.
 *
 * @since 1.0.0
 */
class Query implements QueryInterface
{
	/* @var \PDO The PDO. */
	private $pdo;

	/* @var array The bindings. */
	private $bindings;

	/* @var QueryBuilder. */
	private $queryBuilder;

	/**
	 * Construct a query object with the given pdo and table.
	 *
	 * @param \PDO $pdo
	 * @param string $table
	 */
	public function __construct(\PDO $pdo, $table)
	{
		$this->pdo = $pdo;
		$this->bindings = [];
		$this->queryBuilder = new QueryBuilder($table);
	}

	/**
	 * Returns a string representation of the query object.
	 *
	 * @return string a string representation of the query object.
	 */
	public function __toString()
	{
		return $this->queryBuilder->__toString();
	}

	/**
	 * {@inheritdoc}
	 */
	public function select($columns = ['*'])
	{
		$this->queryBuilder->select($columns);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function insert(array $values)
	{
		$this->queryBuilder->insert($this->replaceValuesWithBindings($values));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update(array $values)
	{
		$this->queryBuilder->update($this->replaceValuesWithBindings($values));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete()
	{
		$this->queryBuilder->delete();

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function where($column, $operator, $value)
	{
		$binding = sprintf(':%s', $column);

		$this->bindings[$binding] = $value;
		$this->queryBuilder->where($column, $operator, $binding);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function limit($limit)
	{
		$this->bindings[':limit'] = $limit;
		$this->queryBuilder->limit(':limit');

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offset($offset)
	{
		$this->bindings[':offset'] = $offset;
		$this->queryBuilder->offset(':offset');

		return $this;
	}

	/**
	 *
	 */
	public function execute()
	{
		$pdoStatement = $this->pdo->prepare((string) $this);
		$pdoStatement->execute($this->bindings);

		return new QueryResult($pdoStatement);
	}

	/**
	 *
	 */
	private function replaceValuesWithBindings($values)
	{
		$result = [];

		foreach ($values as $key => $value) {
			$binding = sprintf(':%s', $key);

			$this->bindings[$binding] = $value;
			$result[$key] = $binding;
		}

		return $result;
	}
}
