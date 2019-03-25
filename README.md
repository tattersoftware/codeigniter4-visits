# Tatter\Visits
Lightweight traffic tracking for CodeIgniter 4

## Quick Start

1. Run: `> composer require tatter/visits`
2. Update the database: `> php spark migrate:latest -n Tatter`
3. Load the service: `$visits = Services::('visits')`
4. Record the visit: `$visits->record();`

## Features

Provides automated traffic tracking for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
`> composer require tatter/visits`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
`> php spark migrate:latest -n Tatter`

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
Visits.php.example to app/Config/Visits.php and follow the instructions in the
comments. If no config file is found in app/Config the library will use its own.

## Usage

If installed correctly CodeIgniter 4 will detect and autoload the class, service, and
config. Get an instance of the service:
`$visits = Services::('visits');`
Then use the `record` method to log the current visit:
`$visits->record();`
**Recommended:** Include both these steps in `BaseController` in the
`initController()` method so traffic is recorded on every load.

## Accessing data

This library provides a `VisitModel` and a `Visit` entity for convenient access to recorded
entries. Feel free to extend these classes for any additional functionality.

## User tracking

It is not legal nor advisable to track user traffic in all cases. By default `Visits` will
only include user IDs when they are loaded into the specific session variable
`visitsUserId`. If this session variable is not set access will be anonymous. You can
change the variable used to determine a logged in user by using the Config file and
altering the value (see above).
