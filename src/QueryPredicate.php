<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */
namespace miBadger\Query;

class QueryPredicate implements QueryExpression
{
	private $type;

	private $conditions;

	public function __construct($type, QueryExpression $left, QueryExpression ...$others)
	{
		switch ($type) {
			case 'AND':
			case 'OR':
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
				break;
		}

		$this->conditions = array_merge([$left], $others);
	}

	public function getFlattenedConditions()
	{
		$conditions = [];

		foreach ($this->conditions as $condition) {
			$conditions = array_merge($conditions, $condition->getFlattenedConditions());
		}

		return $conditions;
	}

	public function __toString()
	{
		$conditionSql;
		foreach ($this->conditions as $cond) {
			$conditionSql[] = sprintf('( %s )', (string) $cond);
		}
		
		$sql = '';
		switch ($this->type) {
			case 'AND':
				$sql = join($conditionSql, ' AND ');
				break;
			case 'OR':
				$sql = join($conditionSql, ' OR ');
				break;
			case 'NOT':
				$sql = sprintf('NOT %s', $conditionSql[0]);
				break;
			default:
				throw new QueryException(sprintf("Invalid predicate operator \"%s\"", $this->type));
				break;
		}
		return $sql;
	}
}