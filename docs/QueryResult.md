# QueryResult

The query result class.

## Example(s)

```php
// Get the query result.
$queryResult = $query->execute();

// Count the number of rows
$queryResult->count();

count($queryResult);

// Use the iterator
$queryResult->getIterator();

foreach ($queryResult as $row) {

}

// Fetch a row
$queryResult->fetch();

// Fetch all rows
$queryResult->fetchAll();
```
