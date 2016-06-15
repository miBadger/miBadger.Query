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
 * The query builder class.
 *
 * @since 1.0.0
 */
class QueryBuilder implements QueryInterface
{
	/* @var string The modifier. SELECT, INSERT INTO, UPDATE or DELETE */
	private $modifier;

	/* @var string The table name. */
	private $table;

	/* @var array The columns. */
	private $columns;

	/* @var array The values. */
	private $values;

	/* @var array The where conditions. */
	private $where;

	/* @var array The group by conditions. */
	private $groupBy;

	/* @var array The order by conditions. */
	private $orderBy;

	/* @var string The limit. */
	private $limit;

	/* @var string The offset. */
	private $offset;

	/**
	 * Construct a query object with the given table.
	 *
	 * @param string $table
	 */
	public function __construct($table)
	{
		$this->table = $table;
		$this->where = [];
		$this->groupBy = [];
		$this->orderBy = [];
	}

	/**
	 * Returns a string representation of the query object.
	 *
	 * @return string a string representation of the query object.
	 */
	public function __toString()
	{
		switch ($this->modifier) {
			case self::SELECT:
				$result = $this->getSelectClause();

				if ($where = $this->getWhereClause()) {
					$result .= ' ' . $where;
				}

				if ($groupBy = $this->getGroupByClause()) {
					$result .= ' ' . $groupBy;
				}

				if ($orderBy = $this->getOrderByClause()) {
					$result .= ' ' . $orderBy;
				}

				if ($limit = $this->getLimitClause()) {
					$result .= ' ' . $limit;
				}

				if ($offset = $this->getOffsetClause()) {
					$result .= ' ' . $offset;
				}

				return $result;

			case self::INSERT:
				$result = $this->getInsertClause();

				return $result;

			case self::UPDATE:
				$result = $this->getUpdateClause();

				if ($where = $this->getWhereClause()) {
					$result .= ' ' . $where;
				}

				if ($limit = $this->getLimitClause()) {
					$result .= ' ' . $limit;
				}

				return $result;

			case self::DELETE:
				$result = $this->getDeleteClause();

				if ($where = $this->getWhereClause()) {
					$result .= ' ' . $where;
				}

				if ($limit = $this->getLimitClause()) {
					$result .= ' ' . $limit;
				}

				return $result;

			default:
				return '';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function select($columns = ['*'])
	{
		$this->columns = is_array($columns) ? $columns : func_get_args();
		$this->modifier = self::SELECT;

		return $this;
	}

	/**
	 * Returns the select clause.
	 *
	 * @return string the select clause.
	 */
	public function getSelectClause()
	{
		return sprintf('SELECT %s FROM %s', implode(', ', $this->columns), $this->table);
	}

	/**
	 * {@inheritdoc}
	 */
	public function insert(array $values)
	{
		$this->values = $values;
		$this->modifier = self::INSERT;

		return $this;
	}

	/**
	 * Returns the insert clause.
	 *
	 * @return string the insert clause.
	 */
	public function getInsertClause()
	{
		$columns = [];
		$values = [];

		foreach ($this->values as $key => $value) {
			$columns[] = $key;
			$values[] = sprintf('%s', $value);
		}

		return sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(', ', $columns), implode(', ', $values));
	}

	/**
	 * {@inheritdoc}
	 */
	public function update(array $values)
	{
		$this->values = $values;
		$this->modifier = self::UPDATE;

		return $this;
	}

	/**
	 * Returns the update clause.
	 *
	 * @return string the update clause.
	 */
	public function getUpdateClause()
	{
		$placeholders = [];

		foreach ($this->values as $key => $value) {
			$placeholders[] = sprintf('%s = %s', $key, $value);
		}

		return sprintf('UPDATE %s SET %s', $this->table, implode(', ', $placeholders));
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete()
	{
		$this->modifier = self::DELETE;

		return $this;
	}

	/**
	 * Returns the delete clause.
	 *
	 * @return string the delete clause.
	 */
	public function getDeleteClause()
	{
		return sprintf('DELETE FROM %s', $this->table);
	}

	/**
	 * {@inheritdoc}
	 */
	public function where($column, $operator, $value)
	{
		$this->where[] = [$column, $operator, $value];

		return $this;
	}

	/**
	 * Returns the where clause.
	 *
	 * @return string the where clause.
	 */
	private function getWhereClause()
	{
		if (empty($this->where)) {
			return '';
		}

		$result = [];

		foreach ($this->where as $key => $value) {
			$result[] = $this->getWhereCondition($value[0], $value[1], $value[2]);
		}

		return sprintf('WHERE %s', implode(' AND ', $result));
	}

	/**
	 * Returns the where condition.
	 *
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @return string the where condition.
	 */
	private function getWhereCondition($column, $operator, $value)
	{
		if ($operator == 'IN') {
			return sprintf('%s IN (%s)', $column, is_array($value) ? implode(', ', $value) : $value);
		} else {
			return sprintf('%s %s %s', $column, $operator, $value);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function groupBy($column)
	{
		$this->groupBy[] = $column;

		return $this;
	}

	/**
	 * Returns the group by clause.
	 *
	 * @return string the group by clause.
	 */
	public function getGroupByClause()
	{
		if (empty($this->groupBy)) {
			return '';
		}

		return sprintf('GROUP BY %s', implode(', ', $this->groupBy));
	}

	/**
	 * {@inheritdoc}
	 */
	public function orderBy($column, $order = null)
	{
		if (strcasecmp($order, 'asc') == 0) {
			$column .= ' ASC';
		} elseif(strcasecmp($order, 'desc') == 0) {
			$column .= ' DESC';
		}

		$this->orderBy[] = $column;

		return $this;
	}

	/**
	 * Returns the order by clause.
	 *
	 * @return string the order by clause.
	 */
	public function getOrderByClause()
	{
		if (empty($this->orderBy)) {
			return '';
		}

		return sprintf('ORDER BY %s', implode(', ', $this->orderBy));
	}

	/**
	 * {@inheritdoc}
	 */
	public function limit($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Returns the limit clause.
	 *
	 * @return string the limit clause.
	 */
	private function getLimitClause()
	{
		if (!$this->limit) {
			return '';
		}

		return sprintf('LIMIT %s', $this->limit);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offset($offset)
	{
		$this->offset = $offset;

		return $this;
	}

	/**
	 * Returns the offset clause.
	 *
	 * @return string the offset clause.
	 */
	private function getOffsetClause()
	{
		if (!$this->limit || !$this->offset) {
			return '';
		}

		return sprintf('OFFSET %s', $this->offset);
	}
}
