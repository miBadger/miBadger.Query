# QueryBuilder

The query builder class.

## Example(s)

```php
<?php

use miBadger\Query\QueryBuilder;

/**
 * Construct a query builder object with the given table.
 */
$queryBuilder = new QueryBuilder($table);

/**
 * Returns a string representation of the query object.
 */
$queryBuilder->__toString();

/**
 * Set the modifier to select and select the given columns.
 */
$queryBuilder->select($columns = ['*']);

/**
 * Set the modifier to insert and insert the given values.
 */
$queryBuilder->insert(array $values);

/**
 * Set the modifier to update and update the given columns.
 */
$queryBuilder->update(array $values);

/**
 * Set the data modifier to delete.
 */
$queryBuilder->delete();

/**
 * Set an additional where condition.
 */
$queryBuilder->where(Query::equal($fieldname, $value));

/**
 * Set an additional group by.
 */
$queryBuilder->groupBy($column);

/**
 * Set an additional order condition.
 */
$queryBuilder->orderBy($column, $order = null);

/**
 * Set the limit.
 */
$queryBuilder->limit($limit);

/**
 * Set the offset.
 */
$queryBuilder->offset($offset);
```
