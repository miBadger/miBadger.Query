<?php

/**
 * This file is part of the miBadger package.
 *
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */

namespace miBadger\Query;

use PHPUnit\Framework\TestCase;

/**
 * The query test class.
 *
 * @since 1.0.0
 */
class QueryPredicateTest extends TestCase
{
	public function testBasic()
	{
		$e1 = Query::Greater(2, 1);
		$e2 = Query::Equal(3, 3);

		$not = new QueryPredicate('NOT', $e1);
		$this->assertEquals('NOT ( 2 > 1 )', (string) $not);

		$and = new QueryPredicate('AND', $e1, $e2);
		$this->assertEquals('( 2 > 1 ) AND ( 3 = 3 )', (string) $and);

		$or = new QueryPredicate('OR', $e1, $e2);
		$this->assertEquals('( 2 > 1 ) OR ( 3 = 3 )', (string) $or);
	}

	public function testMultipleArguments()
	{
		$e = Query::Equal(3, 3);
		$multipleAnd = new QueryPredicate('AND', $e, $e, $e);		// @TODO: Should we expect this to work?
		$this->assertEquals('( 3 = 3 ) AND ( 3 = 3 ) AND ( 3 = 3 )', (string) $multipleAnd);

		$multipleOr = new QueryPredicate('OR', $e, $e, $e);
		$this->assertEquals('( 3 = 3 ) OR ( 3 = 3 ) OR ( 3 = 3 )', (string) $multipleOr);
	}

	public function testNotEnoughArgumentsException()
	{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('AND Operator needs at least two arguments');

		new QueryPredicate('AND', Query::Equal(3, 3));
	}

	public function testTooManyArgumentsException()
	{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('NOT Operator can only accept 1 argument');

		new QueryPredicate('NOT', Query::Equal(3, 3), Query::Equal(3, 3));
	}

	public function testInvalidOperatorException()
	{
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('Invalid predicate operator "FOO"');
		new QueryPredicate('FOO', Query::Equal(3, 3), Query::Equal(3, 3));
	}

	public function testCaseInsensitivity()
	{
		$lower = new QueryPredicate('not', Query::Equal(3, 3));
		$this->assertEquals('NOT ( 3 = 3 )', (string) $lower);
	}
}
