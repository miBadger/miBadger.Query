<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 */
namespace miBadger\Query;

interface QueryExpression
{
	/**
	 * @return Array array of expressions that appear in this 
	 */
	public function getFlattenedConditions();
	
	/**
	 * Returns the SQL Representation of this query expression as a string
	 * @return string The SQL clause
	 */
	public function __toString();
}