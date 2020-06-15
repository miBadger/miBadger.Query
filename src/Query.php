<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
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

	/* @var QueryExpression The expression for the where clause */
	private $whereExpression;

	/* @var QueryExpression The expression for the having clause */
	private $havingExpression;

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
		$this->whereExpression = null;
		$this->havingExpression = null;
		$this->queryBuilder = new QueryBuilder($table);
	}

	/**
	 * Returns a string representation of the query object.
	 *
	 * @return string a string representation of the query object.
	 */
	public function __toString()
	{
		return $this->toPreparedString();
	}

	/**
	 * Binds the registered Where and Having expression clauses, 
	 * 	before being executed (or printed)
	 */
	private function addLateBindings()
	{
		// Late binding of where statement
		if ($this->whereExpression !== null) {
			foreach ($this->whereExpression->getFlattenedConditions() as $cond) {
				$cond->bind($this, 'where');
			}
		}

		// Late binding of having statement
		if ($this->havingExpression !== null) {
			foreach ($this->havingExpression->getFlattenedConditions() as $cond) {
				$cond->bind($this, 'having');
			}
		}		
	}

	/**
	 * Removes the late-bound bindings for the Where/Having expression clauses,
	 * 	used to make debug strings possible
	 */
	private function clearLateBindings()
	{
		// Remove the binding from the QueryConditions and remove the bound values.
		if ($this->whereExpression !== null) {
			foreach ($this->whereExpression->getFlattenedConditions() as $cond) {
				$cond->clearBinding();
			}
		}
		$this->removeBindings('where');

		if ($this->havingExpression !== null) {
			foreach ($this->havingExpression->getFlattenedConditions() as $cond) {
				$cond->clearBinding();
			}
		}
		$this->removeBindings('having');
	}

	/**
	 * Returns the prepared SQL string, where binding substitutions have been applied
	 * Example: SELECT * FROM table WHERE name = :where1
	 */
	public function toPreparedString()
	{
		$this->addLateBindings();
		return $this->queryBuilder->__toString();
	}

	/**
	 * Returns the SQL string without prepared statements, useful for debugging or logging purposes
	 * Example: SELECT * FROM table WHERE name = John Doe
	 */
	public function toDebugString()
	{
		$this->clearLateBindings();
		return $this->queryBuilder->__toString();
	}

	/**
	 * {@inheritdoc}
	 */
	public function select(Array $columns = ['*'], bool $quote = true)
	{
		if ($quote === true && $columns !== ['*']) {
			$quotedColumns = [];
			foreach ($columns as $column) {
				$quotedColumns[] = '`' . $column . '`';
			}
			$this->queryBuilder->select($quotedColumns);
		} else {
			$this->queryBuilder->select($columns);
		}

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
	public function where(QueryExpression $exp)
	{
		$this->whereExpression = $exp;

		if ($this->queryBuilder->where !== null) {
			throw new QueryException('Can only call where on query once.');
		}

		$this->queryBuilder->where($exp);
		return $this;
	}

	public function having(QueryExpression $exp)
	{
		$this->havingExpression = $exp;

		if ($this->queryBuilder->having !== null) {
			throw new QueryException('Can only call having on query once.');
		}

		$this->queryBuilder->having($exp);
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

		$this->clearLateBindings();

		return new QueryResult($pdoStatement);
	}

	/**
	 * Returns the data type of the given value.
	 *
	 * @param mixed $value
	 * @return int the data type of the given value.
	 */
	protected function getPdoDataType($value)
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
	public function addBinding($clause, $value)
	{
		$this->bindings[$clause][] = $value;

		return sprintf(':%s%d', $clause, count($this->bindings[$clause]));
	}

	/**
	 * Returns bindings for the given clause and values.
	 *
	 * @param string $clause
	 * @param array $values
	 * @return array bindings for the given clause and values.
	 */
	public function addBindings($clause, array $values)
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
	 * Returns bindings for the given clause and values.
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
	 * Remove the bindings that are associated with the given clause.
	 *
	 * @param string $clause
	 * @return $this
	 */
	private function removeBindings($clause)
	{
		$this->bindings[$clause] = [];

		return $this;
	}

	/**
	 * Creates a Greater than Query condition, equivalent to mysql > operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function Greater($left, $right): QueryCondition
	{
		return new QueryCondition($left, '>', $right);
	}

	/**
	 * Creates a "Greater than or equal to" Query condition, equivalent to mysql >= operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function GreaterOrEqual($left, $right): QueryCondition
	{
		return new QueryCondition($left, '>=', $right);
	}

	/**
	 * Creates a "Less than" Query condition, equivalent to mysql < operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function Less($left, $right): QueryCondition
	{
		return new QueryCondition($left, '<', $right);
	}

	/**
	 * Creates a "Less than or equal to" Query condition, equivalent to mysql <= operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function LessOrEqual($left, $right): QueryCondition
	{
		return new QueryCondition($left, '<=', $right);
	}

	/**
	 * Creates an "equal to" Query condition, equivalent to mysql = operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function Equal($left, $right): QueryCondition
	{
		return new QueryCondition($left, '=', $right);
	}

	/**
	 * Creates a "Not equal to" Query condition, equivalent to mysql <> or != operators
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function NotEqual($left, $right): QueryCondition
	{
		return new QueryCondition($left, '<>', $right);
	}

	/**
	 * Creates a "Not Like" Query condition, equivalent to mysql NOT LIKE operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function NotLike($left, $right): QueryCondition
	{
		return new QueryCondition($left, 'NOT LIKE', $right);
	}

	/**
	 * Creates a "Like" Query condition, equivalent to mysql LIKE operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function Like($left, $right): QueryCondition
	{
		return new QueryCondition($left, 'LIKE', $right);
	}

	/**
	 * Creates an "Is" Query condition, equivalent to mysql IS operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param mixed $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function Is($left, $right): QueryCondition
	{
		return new QueryCondition($left, 'IS', $right);
	}

	/**
	 * Creates an "Is not" Query condition, equivalent to mysql IS NOT operator
	 * @param string $left the lhs of the condition. Unescaped
	 * @param string|array $right the rhs of the condition. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function IsNot($left, $right): QueryCondition
	{
		return new QueryCondition($left, 'IS NOT', $right);
	}

	/**
	 * Creates a "Not in" Query condition, equivalent to mysql NOT IN operator
	 * @param string $needle the parameter that cannot be present in the haystack. Unescaped
	 * @param string|Array $haystack the values that can be searched through. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function NotIn($needle, $haystack): QueryCondition
	{
		return new QueryCondition($needle, 'NOT IN', $haystack);
	}

	/**
	 * Creates a "In" Query condition, equivalent to mysql IN operator
	 * @param string $needle the parameter that has to be found. Unescaped
	 * @param string|Array $haystack the values that can be searched through. Escaped
	 * @return QueryCondition the query condition
	 */
	public static function In($needle, $haystack): QueryCondition
	{
		return new QueryCondition($needle, 'IN', $haystack);
	}

	/**
	 * Creates an "AND" predicate from a variable number of expressions
	 * @return QueryPredicate the predicate expression
	 */
	public static function And(QueryExpression $left, QueryExpression ...$others): ?QueryPredicate
	{
		return new QueryPredicate('AND', $left, ...$others);
	}

	/**
	 * Combines an array of QueryExpression clauses into an AND predicate
	 * @return miBadger\Query\QueryExpression|null Either null (if array contains no clauses), 
	 * 				the single clause in the input array, or a QueryPredicate combining the clauses
	 */
	public static function AndArray(Array $clauses): ?QueryPredicate
	{
		if (count($clauses) == 0) {
			return null;
		} else if (count($clauses) == 1) 
		{
			return $clauses[0];
		} else {
			return new QueryPredicate('AND', $clauses[0], ...array_slice($clauses, 1));
		}
	}

	/**
	 * Creates an "OR" predicate from a variable number of expressions
	 * @return QueryPredicate the predicate expression
	 */
	public static function Or(QueryExpression $left, QueryExpression ...$others): QueryPredicate
	{
		return new QueryPredicate('OR', $left, ...$others);
	}

	/**
	 * Combines an array of QueryExpression clauses into an OR predicate
	 * @return miBadger\Query\QueryExpression|null Either null (if array contains no clauses), 
	 * 				the single clause in the input array, or a QueryPredicate combining the clauses
	 */
	public static function OrArray(Array $clauses): ?QueryPredicate
	{
		if (count($clauses) == 0) {
			return null;
		} else if (count($clauses) == 1)
		{
			return $clauses[0];
		} else {
			return new QueryPredicate('OR', $clauses[0], ...array_slice($clauses, 1));
		}
	}

	/**
	 * Creates a "NOT" predicate negating an expression
	 * @param QueryExpression The condition to be negated
	 * @return QueryPredicate the predicate expression
	 */
	public static function Not(QueryExpression $exp): QueryPredicate
	{
		return new QueryPredicate('NOT', $exp);
	}
}
