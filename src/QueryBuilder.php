<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
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

	/* @var array The join conditions. */
	private $join;

	/* @var QueryExpression The where clause. */
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
	 * Construct a query builder object with the given table.
	 *
	 * @param string $table
	 */
	public function __construct($table)
	{
		$this->table = $table;
		$this->join = [];
		$this->where = null;
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
				return $this->getSelectQuery();

			case self::INSERT:
				return $this->getInsertQuery();

			case self::UPDATE:
				return $this->getUpdateQuery();

			case self::DELETE:
				return $this->getDeleteQuery();

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
	private function getSelectClause()
	{
		return sprintf('SELECT %s FROM %s', implode(', ', $this->columns), $this->table);
	}

	/**
	 * Returns the select query.
	 *
	 * @return string the select query.
	 */
	private function getSelectQuery()
	{
		$result = $this->getSelectClause();

		if ($join = $this->getJoinClause()) {
			$result .= ' ' . $join;
		}

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
	private function getInsertClause()
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
	 * Returns the insert query.
	 *
	 * @return string the insert query.
	 */
	private function getInsertQuery()
	{
		return $this->getInsertClause();
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
	private function getUpdateClause()
	{
		$placeholders = [];

		foreach ($this->values as $key => $value) {
			$placeholders[] = sprintf('%s = %s', $key, $value);
		}

		return sprintf('UPDATE %s SET %s', $this->table, implode(', ', $placeholders));
	}

	/**
	 * Returns the update query.
	 *
	 * @return string the update query.
	 */
	private function getUpdateQuery()
	{
		$result = $this->getUpdateClause();

		if ($where = $this->getWhereClause()) {
			$result .= ' ' . $where;
		}

		if ($limit = $this->getLimitClause()) {
			$result .= ' ' . $limit;
		}

		return $result;
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
	private function getDeleteClause()
	{
		return sprintf('DELETE FROM %s', $this->table);
	}

	/**
	 * Returns the delete query.
	 *
	 * @return string the delete query.
	 */
	private function getDeleteQuery()
	{

		$result = $this->getDeleteClause();

		if ($where = $this->getWhereClause()) {
			$result .= ' ' . $where;
		}

		if ($limit = $this->getLimitClause()) {
			$result .= ' ' . $limit;
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function join($table, $primary, $operator, $secondary)
	{
		$this->join[] = ['INNER JOIN', $table, $primary, $operator, $secondary];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function leftJoin($table, $primary, $operator, $secondary)
	{
		$this->join[] = ['LEFT JOIN', $table, $primary, $operator, $secondary];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rightJoin($table, $primary, $operator, $secondary)
	{
		$this->join[] = ['RIGHT JOIN', $table, $primary, $operator, $secondary];

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function crossJoin($table, $primary, $operator, $secondary)
	{
		$this->join[] = ['CROSS JOIN', $table, $primary, $operator, $secondary];

		return $this;
	}

	private function getJoinClause()
	{
		$result = [];

		foreach ($this->join as $key => $value) {
			$result[] = sprintf('%s %s ON %s %s %s', $value[0], $value[1], $value[2], $value[3], $value[4]);
		}

		return implode(' ', $result);
	}

	/**
	 * {@inheritdoc}
	 */
	public function where(QueryExpression $whereClause)
	{
		$this->where = $whereClause;

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

		return sprintf('WHERE %s', (string) $this->where);
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
	private function getGroupByClause()
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
	private function getOrderByClause()
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
