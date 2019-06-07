<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */
namespace miBadger\Query;

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

	public function __construct($left, $operator, $right)
	{
		$this->leftOperand = $left;
		$this->operator = $operator;
		$this->binding = null;

		switch ($operator) {
			case '<':
			case '>':
			case '=':
			case '<>':
			case 'LIKE':
			case 'NOT LIKE':
			case 'IS':
			case 'IS NOT':
				$this->rightOperand = $right;
				break;

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

	public function bind(Query $query)
	{
		if (is_array($this->rightOperand)) {
			$this->binding = $query->addBindings('where', $this->rightOperand);
		} else {
			$this->binding = $query->addBinding('where', $this->rightOperand);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		if ($this->binding === null) {
			$rhs = $this->rightOperand;
			// throw new QueryException("Currently unbound conditions are not supported!");
		} else {
			$rhs = $this->binding;
		}

		if (is_array($rhs)) {
			$rhs = sprintf('(%s)', implode(', ', $rhs));
		}

		return sprintf('%s %s %s', $this->leftOperand, $this->operator, $rhs);
	}
}
