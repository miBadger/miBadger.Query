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

	/* @var QueryBuilder The query builder. */
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
		$this->queryBuilder->insert($this->replaceValuesWithBindings($values, 'insert'));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update(array $values)
	{
		$this->queryBuilder->update($this->replaceValuesWithBindings($values, 'update'));

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
		if ($operator == 'IN') {
			$this->whereIn($column, $value);
		} else {
			$this->bindings['where'][] = $value;
			$this->queryBuilder->where($column, $operator, sprintf(':where%s', count($this->bindings['where'])));
		}

		return $this;
	}

	/**
	 * Set an additional where in condition.
	 *
	 * @param string $column
	 * @param mixed $values
	 * @return $this
	 */
	private function whereIn($column, $values)
	{
		$bindings = [];

		foreach ($values as $value) {
			$this->bindings['where'][] = $value;

			$bindings[] = sprintf(':where%s', count($this->bindings['where']));
		}

		$this->queryBuilder->where($column, 'IN', $bindings);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function groupBy($column)
	{
		$this->queryBuilder->groupBy($column);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function orderBy($column, $order = null)
	{
		$this->queryBuilder->orderBy($column, $order);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function limit($limit)
	{
		$this->bindings['limit'][] = (int) $limit;
		$this->queryBuilder->limit(':limit1');

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offset($offset)
	{
		$this->bindings['offset'][] = (int) $offset;
		$this->queryBuilder->offset(':offset1');

		return $this;
	}

	/**
	 * Returns the result of the executed prepared query.
	 *
	 * @return QueryResult the result of the executed prepared query.
	 */
	public function execute()
	{
		$pdoStatement = $this->pdo->prepare((string) $this);

		foreach ($this->bindings as $clause => $predicate) {
			foreach ($predicate as $key => $value) {
				if (is_bool($value)) {
					$type = \PDO::PARAM_BOOL;
				} elseif (is_null($value)) {
					$type = \PDO::PARAM_NULL;
				} elseif (is_int($value)) {
					$type = \PDO::PARAM_INT;
				} else {
					$type = \PDO::PARAM_STR;
				}

				$pdoStatement->bindValue(sprintf(':%s%s', $clause, $key + 1), $value, $type);
			}
		}

		$pdoStatement->execute();

		return new QueryResult($pdoStatement);
	}

	/**
	 * Returns the values array with bindings instead of values.
	 *
	 * @param array $values
	 * @return array the values array with bindings instead of values.
	 */
	private function replaceValuesWithBindings(array $values, $clause)
	{
		$result = [];

		foreach ($values as $key => $value) {
			$this->bindings[$clause][] = $value;

			$result[$key] = sprintf(':%s%s', $clause, count($this->bindings[$clause]));
		}

		return $result;
	}
}
