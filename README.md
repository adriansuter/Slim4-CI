# Slim4-CI

This repository contains a small [Slim4](https://github.com/slimphp/Slim) application
that initially does not define a specific PSR-7 implementation. It uses
Continuous Integration (Travis-CI) to build and check the application 
against the four PSR-7 implementations supported by default by the slim 
framework.


## Status

| #   | PSR-7 Implementation | Status         |
| --- | -------------------- | -------------- |
| 1   | Slim PSR-7           | [![Slim](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/1)](https://travis-ci.org/adriansuter/Slim4-CI)              |
| 2   | Nyholm               | [![Nyholm](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/2)](https://travis-ci.org/adriansuter/Slim4-CI)            |
| 3   | Guzzle               | [![Guzzle](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/3)](https://travis-ci.org/php-http/psr7-integration-tests) |
| 4   | Zend                 | [![Zend](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/4)](https://travis-ci.org/php-http/psr7-integration-tests)   |

*Note: Travis-CI is configured to be triggered automatically at least every 24 hours.*


## Explanation

When triggered, Travis-CI creates four different virtual machines. Each installs
an Apache web server and this application. The web server is configured such
that `http://localhost/` points to the subdirectory `public`. Then each virtual 
machine would require via composer a different PSR-7 implementation.

Eventually the PHPUnit test is launched which would use the Guzzle HTTP
Client to make http requests and validate the responses.


### Files

**Web Application**

- `public/.htaccess` This file contains the server override settings.
- `public/index.php` This is the entry point on the server.
- `composer.json` This is the composer file.

**Travis-CI**

- `build/travis-ci-apache.conf` The apache configuration.
- `.travis.yml` The travis configuration.

**Tests**

- `tests/bootstrap.php` The tests bootstrap.
- `tests/*Test.php` The PHPUnit test classes.


## Development

Help in form of issues or pull requests would be very much welcomed.

- Clone your fork of this repository.
- Create a new branch for every patch, feature or improvement.
- Install the required libraries.
  ```bash
  $ composer install
  ```
- Decide which PSR-7 implementation you would like to use during development and 
  install it using composer. **Note that this would modify `composer.json` and 
  therefore you must make sure that you do not commit those changes.**
  - Slim  
    ```bash
    $ composer require slim/psr7
    ```
  - Nyholm
    ```bash
    $ composer require nyholm/psr7 nyholm/psr7-server
    ```
  - Guzzle
    ```bash
    $ composer require guzzlehttp/psr7 http-interop/http-factory-guzzle
    ```
  - Zend
    ```bash
    $ composer require zendframework/zend-diactoros
    ```
- Install a web server, make sure that `localhost` points to the `public/` 
  subdirectory and start the web server.
- Run the tests
  ```bash
  $ vendor/bin/phpunit
  ```

By default you will get 1 skipped test. That test works only, if the environment 
variable `PSR7` is set to either `Slim`, `Nyholm`, `Guzzle` or `Zend`.
