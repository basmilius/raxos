# DateTime route round trips

The DateTime path pattern accepts its ISO representation, including `T`, `Z`, signed offsets and up to six fractional digits. URL generation encodes structural characters and routing decodes the captured value once.

```php
use Raxos\DateTime\DateTime;

$date = DateTime::fromString('2026-02-28T12:30:45.123456+01:00');
$url = $router->url([ScheduleController::class, 'show'], ['date' => $date]);
```

ISO-shaped input validates calendar dates, hours, minutes, seconds and offsets rather than silently normalizing impossible dates. Existing natural-language DateTime parsing and the earlier route format remain available. Restrict application input to an explicit format when natural-language parsing is inappropriate.
