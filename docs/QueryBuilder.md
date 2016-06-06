# QueryBuilder

The query builder class.

## Example(s)

```php
$queryBuilder = (new QueryBuilder('table'))
	->select()
	->where('key', '=', 'value')
	->limit(1)
	->offset(1);
```
