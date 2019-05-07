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


## Files

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
