# Stuff Versioned
[![Coverage Status](https://coveralls.io/repos/github/andreypostal/php-stuff-versioned/badge.svg?branch=main&t=1)](https://coveralls.io/github/andreypostal/php-stuff-versioned?branch=main&t=1)

This is a light library with zero dependency that provides
a version manager and interfaces for version
control anything and with any desired backend (like file, mysql, mongodb, postgresql...)

## Installation

```
composer require andreypostal/php-stuff-versioned
```

## Available backends

*soon*

## Usage

Create your versions implementing the [VersionInterface](./src/VersionInterface.php).
This can be used to version stuff like data seed, data migrations, system updates....

The versioning process is controlled by the ID and can be used dynamically, like to perform
versioning based on users or organization.

[BackendInterface](./src/BackendInterface.php) defines the structure
needed to persist the versioning progress/history and will be used as checkpoints
in the versioning process, ensuring we don't re-execute previous successful versions.

The only actual code shipped by this library is the [VersionManager](./src/VersionManager.php)
which only control the version list and the execution (run process) ensuring that we do not execute
the same version id twice.

The order of version inclusion in the VersionManager maters and old versions should never
change position or be removed. If some old version should not be executed on new environments, you should just
skip if by returning 'false' in the check process.

Some simple library usage may look like:

```php

$manager = new \Andrey\StuffVersioned\VersionManager(
    new MongoBackend(), // the chosen backend
    $logger, // this is optional
);
$manager->addVersion(new MyVersion_2025_03_01());
$manager->addVersion(new MyVersion_2025_03_02());
// or
$manager = $manager
    ->withVersion(new MyVersion_2025_03_03())
    ->withVersion(new MyVersion_2025_03_04());
    
// New versions should be added here at the end

$manager->run();
```

And your version implementation may look like:

```php

class MyVersion_2025_03_01 implements \Andrey\StuffVersioned\VersionInterface
{
    public function check(): bool
    {
        return true;
    }

    public function run(): void
    {
        // Create some data    
    }

    public function rollback(): void
    {
        // Delete partially created data    
    }

    public function getId(): string
    {
        return 'MyVersion_2025_03_01';
    }
}

```