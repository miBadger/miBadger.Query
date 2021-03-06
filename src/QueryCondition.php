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
class QueryCondition implements QueryExpression
{
	/* @var string The lhs of the query condition. */
	private $leftOperand;

	/* @var boolean|string|int|Array The rhs of the query condition. */
	private $rightOperand;

	/* @var string The binary operator of this condition. */
	private $operator;

	/* @var string|Array The binding(s) for the rhs of the condition */
	private $binding;

	/**
	 * Constructs a new Query condition for the supplied operator and operands
	 * @param mixed $left the lhs of the expression (Not escaped!)
	 * @param string $operator the SQL operator
	 * @param mixed $right the rhs of the expression (escaped)
	 */
	public function __construct($left, string $operator, $right)
	{
		$this->leftOperand = $left;
		$this->operator = $operator;
		$this->binding = null;

		switch (strtoupper($operator)) {
			case '<':
			case '<=':
			case '>=':
			case '>':
			case '=':
			case '<>':
			case 'LIKE':
			case 'NOT LIKE':
			case 'IS':
			case 'IS NOT':
				if (is_array($right)) {
					throw new QueryException("Array Unsupported for this operand type");
				}
				$this->rightOperand = $right;
				break;

			case 'NOT IN':
			case 'IN':
				if (is_string($right)) {
					$this->rightOperand = explode(', ', $right);
				} else {
					$this->rightOperand = $right;
				}
				break;

			default:
				throw new QueryException(sprintf("Unsupported operator \"%s\"", $operator));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFlattenedConditions()
	{
		return [$this];
	}

	/**
	 * Adds a binding to the supplied query for these query condition parameters
	 * @param miBadger\Query\Query $query the Query to bind to
	 * @param string $bindingClause The clause to bind to (WHERE, HAVING)
	 */
	public function bind(Query $query, string $bindingClause)
	{
		if (is_array($this->rightOperand)) {
			$this->binding = $query->addBindings($bindingClause, $this->rightOperand);
		} else {
			$this->binding = $query->addBinding($bindingClause, $this->rightOperand);
		}
	}


	public function clearBinding()
	{
		$this->binding = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		if ($this->binding === null) {
			$rhs = $this->rightOperand;
		} else {
			$rhs = $this->binding;
		}

		if (is_array($rhs)) {
			$rhs = sprintf('(%s)', implode(', ', $rhs));
		}

		return sprintf('%s %s %s', $this->leftOperand, $this->operator, $rhs);
	}
}
