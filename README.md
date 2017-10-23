# Octris PHP Framework

## Frozen -- 2017/10/24

This repository is frozen and will probably not be updated anylonger. It will stay on github
forever for historic reasons and to keep development history available. Most parts of the framework
(eg.: template engine, validation library, etc.) will be split out into their own repositories and
development will be continued there.

---

## Preface

This is a BSD licensed PHP framework (please see LICENSE file supplied with this repository
for details). This framework is a work in progress and parts of it may change in future.

## Documentation

* Source documentation (nightly update): http://octris.org/doc/octris/core/html/

## Requirements

The framework is developed and tested using Mac OS X and Linux, so you should not
have any problems to use it on either of these operating systems.

The framework requires:

*   PHP 5.6.x or PHP 7.x.x
*   [octris tool](https://github.com/octris/octris/releases)
*   [composer](https://getcomposer.org/)

The OCTRiS framework encourages the usage of composer and the framework tool creates basic composer
configuration files for newly created projects.

## Standards

The framework is compliant to the following standards:

* [PSR-2](http://www.php-fig.org/psr/psr-2/) &mdash; Coding Style Guide
* [PSR-3](http://www.php-fig.org/psr/psr-3/) &mdash; Logger Interface
* [PSR-4](http://www.php-fig.org/psr/psr-4/) &mdash; Improved Autoloading

## Required PHP extensions

The following PHP extensions are required. Without them, the framework might not work properly or
even not at all:

*   [yaml](http://pecl.php.net/package/yaml)
*   bcmath
*   gettext
*   intl
*   mbstring

The following PHP extensions are highly recommended, but not required to be installed. The core
framework will work perfectly without them:

*   readline

## Copyright

Copyright (c) 2011-2017 by Harald Lapp <harald@octris.org>
