# Running Tests

## Prerequisites

### PHPUnit
Unit tests use PHPUnit framework (see http://www.phpunit.de for more information). PHPUnit can be installed via Composer together with other development dependencies using the following command from the command line.

```
php composer install --dev
```

If you don't have composer, you need to install it:
  1. [Get Composer and Follow Installation instructions here](https://getcomposer.org/download )
  2. Be sure to [install Composer **globally**](https://getcomposer.org/doc/00-intro.md#globally): `mv composer.phar /usr/local/bin/composer`

## Running

Once the prerequisites are installed, run the tests from the project root directory:

```
vendor/bin/phpunit
```


If the tests are successful, you should see something similar to this. Otherwise, the errors will be displayed.
```txt
PHPUnit 9.5.11 by Sebastian Bergmann and contributors.

........................................                      1138 / 1138 (100%)

Time: 00:16.616, Memory: 60.00 MB

Tests: 1138, Assertions: 2682.

Generating code coverage report in Clover XML format ... done [00:00.155]

Generating code coverage report in HTML format ... done [00:01.842]
```

## Test coverage report

Code coverage reports are automatically generated by the `phpunit.xml` config files and will be available in `_meta/coverage` once tests have been run.