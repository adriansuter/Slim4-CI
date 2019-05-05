# Slim4-CI

This repository would automatically try to run a simple [Slim4](https://github.com/slimphp/Slim) 
application using four different PSR-7 implementations. To do that, it uses the 
travis-ci system.

## Status

| #   | PSR7 Implementation | Status         |
| --- | ------------------- | -------------- |
| 1   | Slim PSR-7          | [![Slim](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/1)](https://travis-ci.org/adriansuter/Slim4-CI)              |
| 2   | Nyholm              | [![Nyholm](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/2)](https://travis-ci.org/adriansuter/Slim4-CI)            |
| 3   | Guzzle              | [![Guzzle](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/3)](https://travis-ci.org/php-http/psr7-integration-tests) |
| 4   | Zend                | [![Zend](https://travis-matrix-badges.herokuapp.com/repos/adriansuter/Slim4-CI/branches/master/4)](https://travis-ci.org/php-http/psr7-integration-tests)   |

## Explanation

When triggered, travis creates four different virtual machines that install
apache, this application and then load via composer the corresponding PSR-7
implementations. Eventually the test would be a php-script that requests
urls from the server and verifies its responses.

## Files

**Web Application**

- `public/.htaccess` This file contains the server override settings.
- `public/index.php` This is the entry point on the server.
- `composer.json` This is the composer file.

**Travis-CI**

- `build/travis-ci-apache` The apache configuration.
- `.travis.yml` The travis configuration.

**"Tests"**

- `tests/test.php` The tests. This file, called by travis, would make server 
  requests and test if the responses are correct. 
