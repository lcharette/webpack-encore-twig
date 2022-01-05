# Style Guide for Contributing

## PHP

All PHP contributions must adhere to [PSR-1](http://www.php-fig.org/psr/psr-1/) and [PSR-2](http://www.php-fig.org/psr/psr-2/) specifications.

In addition:

### Documentation

- All documentation blocks must adhere to the [PHPDoc](https://phpdoc.org/) format and syntax.
- All PHP files MUST contain the following documentation block immediately after the opening `<?php` tag:

```
/*
 * Webpack Encore Twig Integration
 *
 * @link      https://github.com/lcharette/webpack-encore-twig
 * @copyright Copyright (c) 2021 Louis Charette
 * @license   https://github.com/lcharette/webpack-encore-twig/blob/main/LICENSE (MIT License)
 */
 ```

### Classes

- All classes MUST be prefaced with a documentation block containing a description and the author(s) of that class.  You SHOULD add other descriptive properties as well.
- All class members and methods MUST be prefaced with a documentation block.  Any parameters and return values MUST be documented, unless return value is `void`.
- The contents of a class should be organized in the following order: constants, member variables, constructor, other magic methods, public methods, protected methods, private methods, and finally, deprecated methods (of any type or visibility).
- Setter methods SHOULD return the parent object.

### Variables

 - All class member variables and local variables MUST be declared in `camelCase`.

### Arrays

 - Array keys MUST be defined using `snake_case`.

## Automatically fixing coding style with PHP-CS-Fixer

[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) can be used to automatically fix PHP code styling. A project specific configuration file ([`.php-cs-fixer.php`](.php-cs-fixer.php)) with a set of rules reflecting theses style guidelines. This tool should be used before submitting any code change to assure the style guidelines are met. 

PHP-CS-Fixer is automatically loaded by Composer and can be used from the project root directory :

```
vendor/bin/php-cs-fixer fix
```
