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
		$this->queryBuilder->select(is_array($columns) ? $columns : func_get_args());

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function insert(array $values)
	{
		$this->bindings['insert'] = [];
		$this->queryBuilder->insert($this->setBindings('insert', $values));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update(array $values)
	{
		$this->queryBuilder->update($this->setBindings('update', $values));

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
	public function join($table, $primary, $operator, $secondary)
	{
		$this->queryBuilder->join($table, $primary, $operator, $secondary);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function leftJoin($table, $primary, $operator, $secondary)
	{
		$this->queryBuilder->leftJoin($table, $primary, $operator, $secondary);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rightJoin($table, $primary, $operator, $secondary)
	{
		$this->queryBuilder->rightJoin($table, $primary, $operator, $secondary);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function crossJoin($table, $primary, $operator, $secondary)
	{
		$this->queryBuilder->crossJoin($table, $primary, $operator, $secondary);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function where($column, $operator, $value)
	{
		if ($operator == 'IN' && is_array($value)) {
			$this->queryBuilder->where($column, 'IN', $this->addBindings('where', $value));
		} else {
			$this->queryBuilder->where($column, $operator, $this->addBinding('where', $value));
		}

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
		$this->queryBuilder->limit($this->setBinding('limit', (int) $limit));

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offset($offset)
	{
		$this->queryBuilder->offset($this->setBinding('offset', (int) $offset));

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
				$pdoStatement->bindValue(sprintf(':%s%d', $clause, $key + 1), $value, $this->getPdoDataType($value));
			}
		}

		$pdoStatement->execute();

		return new QueryResult($pdoStatement);
	}

	/**
	 * Returns the data type of the given value.
	 *
	 * @param mixed $value
	 * @return int the data type of the given value.
	 */
	private function getPdoDataType($value)
	{
		$result = \PDO::PARAM_STR;

		if (is_bool($value)) {
			$result = \PDO::PARAM_BOOL;
		} elseif (is_null($value)) {
			$result = \PDO::PARAM_NULL;
		} elseif (is_int($value)) {
			$result = \PDO::PARAM_INT;
		}

		return $result;
	}

	/**
	 * Returns a binding for the given clause and value.
	 *
	 * @param string $clause
	 * @param string $value
	 * @return string a binding for the given clause and value.
	 */
	private function addBinding($clause, $value)
	{
		$this->bindings[$clause][] = $value;

		return sprintf(':%s%d', $clause, count($this->bindings[$clause]));
	}

	/**
	 * Return bindings for the given clause and values.
	 *
	 * @param string $clause
	 * @param array $values
	 * @return array bindings for the given clause and values.
	 */
	private function addBindings($clause, array $values)
	{
		$result = [];

		foreach ($values as $key => $value) {
			$result[$key] = $this->addBinding($clause, $value);
		}

		return $result;
	}

	/**
	 * Returns a binding for the given clause and value.
	 *
	 * @param string $clause
	 * @param string $value
	 * @return string a binding for the given clause and value.
	 */
	private function setBinding($clause, $value)
	{
		return $this->removeBindings($clause)->addBinding($clause, $value);
	}

	/**
	 * Return bindings for the given clause and values.
	 *
	 * @param string $clause
	 * @param array $values
	 * @return array bindings for the given clause and values.
	 */
	private function setBindings($clause, array $values)
	{
		return $this->removeBindings($clause)->addBindings($clause, $values);
	}

	/**
	 * Remove the bindings that are associated with the given caluse.
	 *
	 * @param string $clause
	 * @return $this
	 */
	private function removeBindings($clause)
	{
		$this->bindings[$clause] = [];

		return $this;
	}
}
