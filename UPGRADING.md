# Upgrade Guide

## Version 1 to 2
***

Version 2 is a complete refactor - please read through the documentation to make sure you
understand how the library works and what is needed for configuration.

**NOTICE: visits are no longer recorded automatically!!** You must apply the filter. If you
want the same "set-and-forget" universal logging as version 1 simply apply the filter globally:
```php
// app/Config/Filters.php

class Filters extends BaseConfig
{
    public $globals = [
        'before' => [
            'csrf',
        ],
        'after' => [
            'visits',
        ],
    ];

    // ...
}
```

Other changes:
* All config properties have been typed and new properties added; if you extended this class in **app/Config/** make sure you update your version
* The `password` field (e.g. `http://user:password@example.com/`) is no longer recorded by default for security purposes; if needed use a Transformer
* The `Visits` service no longer exists; remove any direct references
* This library now relies on the Request Service returning an `IncomingRequest` instance; if you have modified the core to change this behavior this library likely won't work for you
