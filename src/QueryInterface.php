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
 * The query interface.
 *
 * @see https://en.wikipedia.org/wiki/SQL
 * @since 1.0.0
 */
interface QueryInterface
{
	const SELECT = 'SELECT';
	const INSERT = 'INSERT INTO';
	const UPDATE = 'UPDATE';
	const DELETE = 'DELETE';

	/**
	 * Set the modifier to select and select the given columns.
	 *
	 * @param array $columns = ['*']
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Queries
	 */
	public function select($columns = ['*']);

	/**
	 * Set the modifier to insert and insert the given values.
	 *
	 * @param array $values
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 */
	public function insert(array $values);

	/**
	 * Set the modifier to update and update the given columns.
	 *
	 * @param array $values
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 */
	public function update(array $values);

	/**
	 * Set the data modifier to delete.
	 *
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Data_manipulation
	 */
	public function delete();

	/**
	 * Set an additional where condition.
	 *
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @return $this
	 * @see https://en.wikipedia.org/wiki/SQL#Operators
	 */
	public function where($column, $operator, $value);

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
