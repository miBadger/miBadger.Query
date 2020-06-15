# Query

The query class.

## Example(s)

```php
<?php

use miBadger\Query\Query;

/**
 * Construct a query object with the given pdo and table.
 */
$query = new Query(\PDO $pdo, $table);

/**
 * Returns a string representation of the query object.
 */
$query->__toString();

/**
 * Set the modifier to select and select the given columns.
 */
$query->select($columns = ['*']);

/**
 * Set the modifier to insert and insert the given values.
 */
$query->insert(array $values);

/**
 * Set the modifier to update and update the given columns.
 */
$query->update(array $values);

/**
 * Set the data modifier to delete.
 */
$query->delete();

/**
 * Set an additional where condition.
 */
$queryBuilder->where(Query::equal($fieldname, $value));

/**
 * Set an additional group by.
 */
$query->groupBy($column);

/**
 * Set an additional order condition.
 */
$query->orderBy($column, $order = null);

/**
 * Set the limit.
 */
$query->limit($limit);

/**
 * Set the offset.
 */
$query->offset($offset);
```

# Query Clauses
The following Query expressions are available (These are just shorthands for the QueryCondition and QueryPredicate classes)
```php
/**
 * Creates a $val > 4 condition
 */
Query::Greater($val, 4);

/**
 * Creates a $val >= 4 condition
 */
Query::GreaterOrEqual($val, 4);

/**
 * Creates a $val < 4 condition
 */
Query::Lesser($val, 4);

/**
 * Cretes a $val <= 4 condition
 */
Query::LessOrEqual($val, 4);

/**
 * Creates a $val = 4 condition
 */
Query::Equal($val, 4);

/**
 * Creates a $val != 4 condition
 */
Query::NotEqual($val, 4);

/**
 * Creates a $val NOT LIKE "FooBar" condition
 */
Query::NotLike($val, "FooBar")

/**
 * Creates a $val LIKE "FooBar" condition
 */
Query::NotLike($val, "FooBar")

/**
 * Creates a $val IS NULL condition
 */
Query::Is($val, NULL)

/**
 * Creates a $val IS NOT NULL condition
 */
Query::IsNot($val, NULL)

/**
 * Creates a $val NOT IN ["foo", "bar"] condition
 */
Query::NotIn($val, ["foo", "bar"])

/**
 * Creates a $val IN ["foo", "bar"] condition
 */
Query::In($val, ["foo", "bar"])


/**
 * Creates an AND predicate
 */
Query::And(Query::Equal(1, 1), Query::Equal(2, 2), Query::Equal(3, 3))

/**
 * Creates an OR predicate
 */
Query::Or(Query::Equal(1, 1), Query::Equal(2, 2), Query::Equal(3, 3))

/**
 * Creates a NOT predicate
 */
Query::Not(Query::Equal(1, 1))
```
