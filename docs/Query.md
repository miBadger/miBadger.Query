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
