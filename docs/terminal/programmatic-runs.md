# Running commands programmatically

```php
$status = $terminal->run(['console.php', 'sync', '--limit=25']);
```

Pass an argv array including the program name. `run()` returns an integer without terminating PHP, so a test or worker can invoke it repeatedly. It uses the same command construction, options, middleware, help and output as `execute()`.

An explicit `$terminal->exit($status)` inside a programmable run becomes that run's return status. Outside a run it retains process-exit behavior. Nested runs restore their enclosing lifecycle after return or failure. `execute()` retains the process entrypoint behavior.

Successful runs return 0. Existing error-status conventions are preserved, including negative parser or command statuses; a host process can map those to its preferred positive OS exit codes. Output still uses the terminal's configured printer and stream.
