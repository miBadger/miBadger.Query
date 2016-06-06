# Query

The query class.

## Example(s)

```php
// Get John Doe's email.
$query = new Query($pdo, $table)
	->select(['email'])
	->where('first_name', 'LIKE', 'John')
	->where('last_name', 'LIKE', 'Doe')
	->limit(1);

// Get the query result.
$queryResult = $query->execute();
```
