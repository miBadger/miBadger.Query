<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */

namespace miBadger\Query;

/**
 * The query interface.
 *
 * @see https://en.wikipedia.org/wiki/SQL
 * @since 1.0.0
 */
interface QueryInterface
{
	/**
	 * Set the modifier to select and select the given columns.
	 *
	 * @param array $columns = ['*']
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 * @see https://en.wikipedia.org/wiki/Select_(SQL)
	 */
	public function select(Array $columns = ['*'], bool $escape = true);

	/**
	 * Set the modifier to insert and insert the given values.
	 *
	 * @param array $values
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 * @see https://en.wikipedia.org/wiki/Insert_(SQL)
	 */
	public function insert(array $values);

	/**
	 * Set the modifier to update and update the given columns.
	 *
	 * @param array $values
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 * @see https://en.wikipedia.org/wiki/Update_(SQL)
	 */
	public function update(array $values);

	/**
	 * Set the data modifier to delete.
	 *
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 * @see https://en.wikipedia.org/wiki/Delete_(SQL)
	 */
	public function delete();

	/**
	 * Set an additional join condition.
	 *
	 * @param string $table
	 * @param string $primary
	 * @param string $operator
	 * @param string $secondary
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function join($table, $primary, $operator, $secondary);

	/**
	 * Set an additional left join condition.
	 *
	 * @param string $table
	 * @param string $primary
	 * @param string $operator
	 * @param string $secondary
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function leftJoin($table, $primary, $operator, $secondary);

	/**
	 * Set an additional right join condition.
	 *
	 * @param string $table
	 * @param string $primary
	 * @param string $operator
	 * @param string $secondary
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function rightJoin($table, $primary, $operator, $secondary);

	/**
	 * Set an additional cross join condition.
	 *
	 * @param string $table
	 * @param string $primary
	 * @param string $operator
	 * @param string $secondary
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function crossJoin($table, $primary, $operator, $secondary);

	/**
	 * Set the where condition
	 *
	 * @param QueryExpression $expression the query expression
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Operators
	 * @see https://en.wikipedia.org/wiki/Where_(SQL)
	 */
	public function where(QueryExpression $expression);

	/**
	 * Set an additional group by.
	 *
	 * @param string $column
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function groupBy($column);

	/**
	 * Set an additional order condition.
	 *
	 * @param string $column
	 * @param string|null $order
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 * @see https://en.wikipedia.org/wiki/Order_by
	 */
	public function orderBy($column, $order = null);

	/**
	 * Set the limit.
	 *
	 * @param mixed $limit
	 * @return $this
	 */
	public function limit($limit);

	/**
	 * Set the offset.
	 *
	 * @param mixed $offset
 	 * @return $this
	 */
	public function offset($offset);
}
