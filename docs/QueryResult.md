# QueryResult

The query result class.

## Example(s)

```php
<?php

use miBadger\Query\QueryResult;

/**
 * Construct a query result object with the given pdo statement.
 */
$queryResult = QueryResult(\PDOStatement $pdoStatement);

/**
 * {@inheritdoc}
 */
$queryResult->getIterator();

/**
 * Fetches a row from the result set.
 */
$queryResult->fetch();

/**
 * Fetches all rows from the result set.
 */
$queryResult->fetchAll();
```
