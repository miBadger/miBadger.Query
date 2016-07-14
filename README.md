# Query

[![Build Status](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/badges/build.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.Query/?branch=master)

The Query Component.

## Example

```php
<?php

use miBadger/Query/Query;

/**
 * Get John Doe's email.
 */
$query = new Query($pdo, $table)
	->select(['email'])
	->where('first_name', 'LIKE', 'John')
	->where('last_name', 'LIKE', 'Doe')
	->limit(1);

/**
 * Get the query result.
 */
foreach ($query->execute() as $row) {
	echo $row['email'];
}
```
