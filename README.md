# Tatter\Visits
Lightweight traffic tracking for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-visits/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-visits/actions/workflows/phpunit.yml)
[![](https://github.com/tattersoftware/codeigniter4-visits/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-visits/actions/workflows/phpstan.yml)
[![](https://github.com/tattersoftware/codeigniter4-visits/workflows/Deptrac/badge.svg)](https://github.com/tattersoftware/codeigniter4-visits/actions/workflows/deptrac.yml)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-visits/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-visits?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/visits`
2. Update the database: `> php spark migrate --all`
3. Apply the `visits` filter in **app/Config/Filters.php**:
```php
class Filters extends BaseConfig
{
    public $globals = [
        'after' => ['visits'],
    ];
...
```

## Features

Provides automated traffic tracking for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
```shell
> composer require tatter/visits
```

Or, install manually by downloading the source files and adding the directory to
**app/Config/Autoload.php**.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is set up correctly:
```shell
> php spark migrate --all
```

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Visits.php** to **app/Config/** and follow the instructions in the
comments. If no config file is found in **app/Config/** the library will use its own.

### Customization

The config file allows for some basic control over what gets counted as a "hit".
* `$ignoreAjax`: Whether to ignore AJAX requests when recording

Filtering by AJAX requests is not a guaranteed business; read more in the [User Guide](https://www.codeigniter.com/user_guide/general/ajax.html).

If you are using the `after` filter method it is also possible to adjust some behaviors based
on the application's Response instance:
* `$ignoreRedirects`: Whether to ignore requests that result in a redirect response
* `$requireBody`: Whether to ignore requests that result in an empty body
* `$requireHtml`: Whether to ignore requests with Content Types other than HTML

## Usage

The main function of this library is applied through a [Controller Filter](https://codeigniter4.github.io/CodeIgniter4/incoming/filters.html).
The `VisitsFilter` is pre-aliased for you as `visits` but needs to be applied to whichever
routes you would like to track. Read the User Guide for more details, but in most cases
applying the filter globally will be the best fit:
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

The filter can be applied to either `before` or `after` methods, with the following expectations:
* `before` filtering is likely to record more nuances in traffic (such as page loads before an error occurs) but they are less customizable
* `after` filtering allows for finer control over what counts as a "hit" but may miss some instances captured by `before`

Applying both `before` and `after` will duplicate your traffic information and should not be done.

## Accessing data

This library provides a `VisitModel` and a `Visit` entity for convenient access to recorded
entries. Feel free to extend these classes for any additional functionality.

## Transformers

Before a visit is assessed for similar and recorded it may be passed through any number of
transformations. A transformer is a class that implements `Tatter\Visits\Interfaces\Transformer`
and has the single static method for applying a transformation:

```php
public static function transform(Visit $visit, IncomingRequest $request): ?Visit;
```

Transformers work on the `Visit` class they are passed, and return either the modified
`Visit` instance or `null` to indicate "don't record this visit" and halt operation. If
a modified `Visit` is returned it will be passed into the next Transformer and so on.

To active Transformers and set their order simply add them to the `$transformers` property
of the config file:
```php
use App\Transformers\AnonymousTransformer;

class Visits extends BaseConfig
{
    public array $transformers = [
        AnonymousTransformer::class,
    ];
}
```

## User tracking

**Visits** will use any Composer package that provides `codeigniter4/authentication-implementation`
to identify an active user. It is not legal nor advisable to track user traffic in all cases,
so make sure you are configuring your project appropriately for local laws and regulations.
Filtering and anonymizing data to meet tighter specifications can be accomplished with Transformers.
