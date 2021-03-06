<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */
namespace miBadger\Query;

/**
 *
 * @since 2.0.0
 */
class QueryPredicate implements QueryExpression
{
	/* @var string The kind of predicate (AND or OR) */
	private $type;

	/* @var Array The list of subclauses that are combined by this predicate. */
	private $conditions;

	/**
	 * Constructs a new query predicate of the given type
	 * @param string $type the predicate type. either 'AND', 'OR' or 'NOT'
	 * @param QueryExpression $left the first query expression to be used by this predicate
	 * @param QueryExpression ...$others the remaining query expressions, if applicable
	 */
	public function __construct(string $type, QueryExpression $left, QueryExpression ...$others)
	{
		$type = strtoupper($type);
		switch ($type) {
			case 'AND':
			case 'OR':
				if (empty($others)) {
					throw new QueryException(sprintf("%s Operator needs at least two arguments", $type));
				}
				$this->type = $type;
				break;

			case 'NOT':
				if (!empty($others)) {
					throw new QueryException("NOT Operator can only accept 1 argument");
				}
				$this->type = $type;
				break;
				
			default:
				throw new QueryException(sprintf("Invalid predicate operator \"%s\"", $type));
		}

		$this->conditions = array_merge([$left], $others);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFlattenedConditions()
	{
		$conditions = [];

		foreach ($this->conditions as $condition) {
			$conditions = array_merge($conditions, $condition->getFlattenedConditions());
		}

		return $conditions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		$conditionSql = [];
		foreach ($this->conditions as $cond) {
			$conditionSql[] = sprintf('( %s )', (string) $cond);
		}
		
		$sql = '';
		switch ($this->type) {
			case 'AND':
				return join(' AND ', $conditionSql);
			case 'OR':
				return join(' OR ', $conditionSql);
			case 'NOT':
				return sprintf('NOT %s', $conditionSql[0]);
			default:
				// return ''; // This case can never happen
		}
	}
}
