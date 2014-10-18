# Octris PHP Framework

## Preface

This is a BSD licensed PHP framework (please see LICENSE file supplied with this repository
for details). This framework is a work in progress and parts of it may change in future.

## Documentation

* Source documentation (nightly update): http://doc.octris.org/org.octris.core/ (currently offline)

## Requirements

It's higly recommended to use this framework in a UN*X environment, like for example Linux and Mac OS X, 
because the framework is untested in a windows environment and will probably not work. The framework is 
developed and tested using Mac OS X, Linux and Solaris (Sparc64), so you should not have any problems to 
use it on either of these operating systems.

The framework requires: 

*   PHP 5.6.x
*   [octris tool](https://github.com/octris/octris/releases)

While not required, the framework is compatible with and it's recommended to use with:

+   [composer](https://getcomposer.org/)

## Standards

The framework is compliant to the following standards:

* [PSR-4](http://www.php-fig.org/psr/psr-4/) -- Autoloading 

## Required PHP extensions

The following PHP extensions are required. Without them, the framework might not work properly or 
even not at all:

*   [yaml](http://pecl.php.net/package/yaml)
*   bcmath
*   gettext
*   intl
*   mbstring
*   mcrypt

The following PHP extensions are highly recommended, but not required to be installed. The core 
framework will work perfectly without them:

*   readline

## Copyright

Copyright (c) 2011-2014 by Harald Lapp <harald@octris.org>
