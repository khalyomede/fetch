# Fetch

![PHP from Packagist](https://img.shields.io/packagist/php-v/khalyomede/fetch.svg)
![Packagist](https://img.shields.io/packagist/v/khalyomede/fetch.svg)
![Packagist](https://img.shields.io/packagist/l/khalyomede/fetch.svg)

Quickly retrieve your PHP data.

From 

```
/config
  /database
    database.php
    option.php
  app.php
```

To

```php
$fetch = new Fetch('config');

$command = $fetch->from('database.option.initial-command');
```

## Summary

- [Prerequistes](#prerequisites)
- [Installation](#installation)
- [Examples of uses](#examples-of-uses)
- [Methods definitions](#methods-definitions)
- [MIT licence](#mit-licence)

## Prerequisites

- PHP version >= 7.0.0

## Installation

In your project folder:

```bash
composer require khalyomede/fetch:1.*
```

## Examples of uses

All the examples below assume we are on the root (inside the `index.php` file) and we have the following arborescence:

```
/config
  /database
    database.php
    option.php
  app.php
index.php
```

- [Example 1: fetching a data from a simple file](#example-1-fetching-a-data-from-a-single-file)
- [Example 2: fetching a data by traversing multiple folders](#example-2-fetching-a-data-by-traversing-multiple-folders)
- [Example 3: fetching a data inside nested keys](#example-3-fetching-a-data-inside-nested-keys)
- [Example 4: fetching some data using a specific key](#example-4-fetching-some-data-using-a-specific-key)
- [Example 5: fetching faster using cache](#example-5-fetching-faster-using-cache)

### Example 1: fetching a data from a simple file

All the configuration file should return a PHP array.

Let us assume the file `app.php` contains:

```php
return [
  'name' => 'My app',
  'charset' => 'utf-8'
];
```
We can fetch the charset using:

```php
use Khalyomede\Fetch;

$fetch = new Fetch( __DIR__ . '/config' );

$charset = $fetch->from('app.charset');

print_r($charset);
```

This will display

```
utf-8
```

### Example 2: fetching a data by traversing multiple folders

All the configuration files should return a PHP array.

Let us assume the file `option.php` contains:

```php
return [
  'initial-command' => "SET names 'utf-8'"
];
```

```php
use Khalyomede\Fetch;

$fetch = new Fetch( __DIR__ . '/config' );

$initial_command = $fetch->from('database.option.initial-command');

print_r($initial_command);
``` 

Will display

```
SET names 'utf-8'
```

### Example 3: fetching a data inside nested keys

All configuration files should return a PHP array.

Let us assume the file `option.php` contains:

```php
return [
  'cache' => [
    'strategy' => 'cache-first'
  ]
];
```

```php
use Khalyomede\Fetch;

$fetch = new Fetch( __DIR__ . '/config' );

$strategy = $fetch->from('database.option.cache.strategy');

print_r($stategy);
```

Will display:

```
cache-first
```

### Example 4: fetching some data using a specific key

All configuration files should return PHP array.

Let us assume the file `option.php` contains:

```php
return [
  'cache' => [
    'strategy' => 'cache-first'
  ],
  'timeout' => 12.5
];
```

```php
use Khalyomede\Fetch;

$fetch = new Fetch( __DIR__ . '/config' );

$data = $fetch->from('database.option');

print_r($data);
```

Will display:

```php
Array
(
  [cache] => Array
    (
      [strategy] => cache-first
    )
  [timeout] => 12.5
)
```

### Example 5: fetching faster using cache

Let us assume a new folder has been created, called `cache`. Here is the updated arborescence:

```
/config
  /database
    option.php
  app.php
/cache
index.php
```

```php
use Khalyomede\Fetch;

$fetch = new Fetch( __DIR__ . '/config' );

$fetch->usingCache( __DIR__ . '/cache' );

$charset = $fetch->from('app.charset');
```

The first fetching might be slow, but the subsequent fetches will be much more faster while the cached data is not removed.

**Note**

This library will not handles the cache removing.

**Tips**

Think to disable the cache when you are in development mode as your configuration file will changed a lot and might be outdated without updating to the new value. This tips features Laravel 5 debug helper.

```php
use Khalyomede\Fetch;

// ...

$fetch = new Fetch( base_path() . '/config' );

if( config('app.debug') !== true ) {
  $fetch->usingCache( base_path() . '/' )
}

$language = $fetch->from('app.locale');
```

**Warning**

The cache might be outdated if you use it without clearing it after an update of your configuration data.

## Methods definitions

- [construct()](#construct)
- [from()](#from)
- [usingCache()](#using-cache)
- [disableCache()](#disable-cache)
- [enableCache()](#enable-cache)

### construct()

Sets the default folder to fetch the data from.

```php
public function __construct(string $folder_path): Fetch
```

**Exceptions**

`InvalidArgumentException`:

- If the folder path does not exists
- If the folder path is not a folder

### from()

Fetch the data from the given path.

```php
public function from(string $path): mixed
```

**Exceptions**

`InvalidArgumentException`:

- If the path is empty
- If one of the key is empty (for example: "database.option.")
- If the targeted configuration file does not return an array
- If the path targets an non existing key

`UnexpectedValueException`:

- If the cache folder has not been specifyied when the cache mode has been enabled

`RuntimeException`:

- If the targeted configuration file is not available is read mode
- If the cached data file is not available in read mode

### usingCache()

Specify the folder for storing the cached data and enable the cache mode.

```php
public function usingCache(string $cache_folder_path): Fetch
```

**Exceptions**

`InvalidArgumentException`:

- If the folder does not exists
- If the path does not target a folder

### enableCache()

Enable the cache mode.

```php
public function enableCache(): Fetch
```

### disableCache()

Disable the cache mode.

```php
public function disableCache(): Fetch
```

## MIT Licence

Fetch

Copyright © 2018 Khalyomede

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the oftware, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN CTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.