---
title: Configuration recommendations
weight: 4
---

Mailcoach will send each mail in a separate job that has its own database connection. We highly recommend make sure that your database can handle at least as many database connections possible connections as the allowed number of jobs that can happen in a second.

So, if you have this configuration in `mailcoach.php`...

```php
'allowed_number_of_jobs_in_timespan' => 30,
'timespan_in_seconds' => 1,
```

... your database should be able to handle at least 30 connection.

You can find out your connection limit by executing this query

```sql
SHOW VARIABLES LIKE "max_connections"
```



