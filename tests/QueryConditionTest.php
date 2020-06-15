<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */

namespace miBadger\Query;

use PHPUnit\Framework\TestCase;

/**
 * The query test class.
 *
 * @since 1.0.0
 */
class QueryConditionTest extends TestCase
{
	public function testUnboundConditions() {
		$c_lt = new Querycondition(1, '<', 2);
		$this->assertEquals('1 < 2', (string) $c_lt);

		$c_leq = new Querycondition(1, '<=', 2);
		$this->assertEquals('1 <= 2', (string) $c_leq);

		$c_geq = new QueryCondition(2, '>=', 1);
		$this->assertEquals('2 >= 1', (string) $c_geq);

		$c_gt = new Querycondition(2, '>', 1);
		$this->assertEquals('2 > 1', (string) $c_gt);

		$c_eq = new Querycondition(2, '=', 2);
		$this->assertEquals('2 = 2', (string) $c_eq);

		$c_neq = new Querycondition(2, '<>', 2);
		$this->assertEquals('2 <> 2', (string) $c_neq);

		$c_like = new QueryCondition('name', 'like', '%a');
		$this->assertEquals('name like %a', (string) $c_like);

		$c_nlike = new QueryCondition('name', 'NOT LIKE', '%a');
		$this->assertEquals('name NOT LIKE %a', (string) $c_nlike);

		$c_nin = new Querycondition('name', 'NOT IN', 'a, b, c');
		$this->assertEquals('name NOT IN (a, b, c)', (string) $c_nin);

		$c_in = new Querycondition('name', 'IN', 'a, b, c');
		$this->assertEquals('name IN (a, b, c)', (string) $c_in);
	}

	public function testArrayParameters() {
		$cond = new Querycondition('name', 'IN', ['A', 'B', 'C']);
		$this->assertEquals('name IN (A, B, C)', (string) $cond);
	}

	public function testInvalidParameterTypeException() {
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('Array Unsupported for this operand type');
		$cond = new Querycondition('age', '>', ['A', 'B', 'C']);
	}

	public function testUnknownOperatorException() {
		$this->expectException(QueryException::class);
		$this->expectExceptionMessage('Unsupported operator "@#$"');
		new QueryCondition('name', '@#$', 'something');
	}

	public function testCapitalization() {
		$lowerCase = new QueryCondition('name', 'like', '%a');
		$this->assertEquals('name like %a', (string) $lowerCase);

		$upperCase = new QueryCondition('name', 'LIKE', '%a');
		$this->assertEquals('name LIKE %a', (string) $upperCase);
	}

	public function testBoundContitions() {
		// First test a single value bind.
		$singleCondition = Query::Like('name', 'John Doe');
		$pdo = new \PDO('sqlite::memory:');
		$query = (new Query($pdo, 'test'))
			->select()
			->where($singleCondition);

		$this->assertEquals('name LIKE John Doe', (string) $singleCondition);
	}
}