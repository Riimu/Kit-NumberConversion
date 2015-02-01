# Arbitrary Precision Base Converter #

*BaseConversion* is a PHP library for converting number bases, similar to PHP's
built in function `base_convert()`. However, unlike the built in function, this
library is not limited by 32 bit integers and is capable of converting numbers
of arbitrary precision. This library also supports conversion of fractions and
allows more customization in terms of number bases.

In order to optimize the conversion of large numbers, this library also employs
two different conversion strategies. In some cases, it's possible to convert
numbers simply by replacing the digits with digits from the other base (e.g.
when converting from base 2 to base 16). This is considerably faster than
the other strategy, which simply calculates the new number using arbitrary
precision integer arithmetic.

The API documentation, which can be generated using Apigen, can be read online
at: http://kit.riimu.net/api/baseconversion/

[![Build Status](https://img.shields.io/travis/Riimu/Kit-BaseConversion.svg?style=flat)](https://travis-ci.org/Riimu/Kit-BaseConversion)
[![Coverage Status](https://img.shields.io/coveralls/Riimu/Kit-BaseConversion.svg?style=flat)](https://coveralls.io/r/Riimu/Kit-BaseConversion?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/Riimu/Kit-BaseConversion.svg?style=flat)](https://scrutinizer-ci.com/g/Riimu/Kit-BaseConversion/?branch=master)

## Requirements ##

In order to use this library, the following requirements must be met:

  * PHP version 5.4
  * GMP extension must be enabled

## Installation ##

This library can be installed via [Composer](http://getcomposer.org/). To do
this, download the `composer.phar` and require this library as a dependency. For
example:

```
$ php -r "readfile('https://getcomposer.org/installer');" | php
$ php composer.phar require riimu/kit-baseconversion:1.*
```

Alternatively, you can add the dependency to your `composer.json` and run
`composer install`. For example:

```json
{
    "require": {
        "riimu/kit-baseconversion": "1.*"
    }
}
```

Any library that has been installed via Composer can be loaded by including the
`vendor/autoload.php` file that was generated by Composer.

It is also possible to install this library manually. To do this, download the
[latest release](https://github.com/Riimu/Kit-BaseConversion/releases/latest) and
extract the `src` folder to your project folder. To load the library, include
the provided `src/autoload.php` file.

## Usage ##

The most convenient way to use this library is via the `baseConvert()` static
method provided by the `BaseConverter` class. In most cases, it works the same
way as `base_convert()` does. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;
echo BaseConverter::baseConvert('A37334', 16, 2); // outputs: 101000110111001100110100
```

The method accepts negative numbers and fractions in the same way. An optional
fourth parameter can be used to define the precision for the conversion. For
example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('-1BCC7.A', 16, 10)  . PHP_EOL; // outputs: -113863.625
echo BaseConverter::baseConvert('-1BCC7.A', 16, 10, 1); // outputs: -113863.6
```

The static method is simply a convenient wrapper for creating an instance of
`BaseConvert` and calling the `setPrecision()` and `convert()` methods. If you
need to convert multiple numbers, it's more efficient to call the object in a
non static manner. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

$converter = new BaseConverter(16, 10);

echo $converter->convert('A37334') . PHP_EOL; // outputs: 10711860
echo $converter->convert('-1BCC7.A')  . PHP_EOL; // outputs: -113863.625

$converter->setPrecision(1);
echo $converter->convert('-1BCC7.A'); // outputs: -113863.6
```

If the provided number contains invalid digits that are not part of the defined
number base, the method will return false instead.

### Converting Fractions ###

While this library does support conversion of fractions, it's important to
understand that fractions cannot always be converted accurately from number base
to another the same way that integers can be converted. This is result of the
fact that not all fractions can be represented in another number base.

For example, let's say we have the number 0.1 in base 3. This equals the same
as 1/3 in base 10. However, if you were to represent 1/3 as a decimal number,
you would get an infinitely repeating '0.3333...'. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('0.1', 3, 10)  . PHP_EOL; // outputs: 0.33
echo BaseConverter::baseConvert('0.1', 3, 10, 6)  . PHP_EOL; // outputs: 0.333333
echo BaseConverter::baseConvert('0.1', 3, 10, 12); // outputs: 0.333333333333
```

Due to this behavior, it is possible to set the precision used for inaccurate
fraction conversions. As can be seen in the previous example, the precision
value defines the maximum number of digits in the resulting number. The result
may have less digits, however, if the number can be accurately converted using
a small number of digits. The precision may also be completely ignored, if the
converter knows, that it can accurately convert the fractions.

The precision value also has an alternative definition. If the precision is
0 or a negative number, then the maximum number of digits in the resulting
number is based on the precision of the original number. If the precision is 0,
the resulting number will have as many digits as it takes to represent the
number in the same precision as the original number. A negative number will
simply increase the number of digits in addition to that. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('0.A7', 16, 10, 0)  . PHP_EOL; // outputs: 0.652
echo BaseConverter::baseConvert('0.A7', 16, 10, -2); // outputs: 0.65234
```

In the previous example, the original number is `0.A7` in the base 16. A base 16
number with two digits in the fractional part can represent a number up to
accuracy of `1/(16 * 16) == 1/256`. To represent the the fractional part in the
same accuracy in base 10, we need at least 3 digits, because two digit can only
represent numbers up to accuracy of `1/100`.

The default precision value used by the library is `-1`. It is also important
to note that the last digit is not rounded (due to the fact that it would
cause inconsistent results in some cases).

### Case Sensitivity ###

In order to make user interaction with the library more convenient, the library
treats all numbers in a case insensitive manner, unless the number base
prohibits that. For example, the base 16 can be treated in a case insensitive
manner, because it only defines the value for the digits `0-9A-F`. However,
base 62 cannot be treated in a case insensitive manner, because letters like
`A` and `a` have a different value.

The returned numbers will always respect the character case defined by the
number base. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('7BFA11', 16, 12)  . PHP_EOL; // outputs: 2879B29
echo BaseConverter::baseConvert('7bfa11', 16, 12); // outputs: 2879B29
```

### Customizing Number Bases ###

One of the features of this library is that allows much better customization of
number bases than `base_convert()`. In most cases, you will probably define the
number base using a simple integer such as `10` or `16`. However, there is no
limit to the size of that integer. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('7F', 16, 1024)  . PHP_EOL; // outputs: #0127
echo BaseConverter::baseConvert('5Glm1z', 64, 512); // outputs: #456#421#310#371
```

For large number bases, however, the digits are simply represented by a string
that consists of `#` and the value for the digit. Whenever the number base is
defined using an integer, the digits follow the following rules:

  * Bases equal or smaller than 62 use digits from the string
    `0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz`
  * A base 64 number uses digits from the base64 standard, i.e.
    `ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/`
  * Other bases equal or smaller than 256 use bytes as digits with byte value
    indicating the digit value.
  * Large bases use strings for digits that consist of `#` and the value for the
    digit (the length of the string depends on the greatest digit value).
    
In addition to defining the number base using an integer, it's also possible
to define the number base using a string. Each character in the string
represents a digit and the position of each character represents it's value.
The base 16, for example, could be defined as `0123456789ABCDEF`. Defining
number bases this way also makes it easier to get resulting numbers in a
specific case. For example:

```php
<?php

require 'vendor/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('101100011101', '01', 16)  . PHP_EOL; // outputs: B1D
echo BaseConverter::baseConvert('101100011101', 2, '0123456789abcdef'); // outputs: b1d
```

There is also a third way to define the number bases using an array. This allows
even greater customization in terms of number bases. Each value in the array
represents a digit and the index indicates the value. For example:

```php
<?php

require 'src/autoload.php';
use Riimu\Kit\BaseConversion\BaseConverter;

echo BaseConverter::baseConvert('22', 10, ['nil', 'one'])  . PHP_EOL; // outputs: oneniloneonenil
echo BaseConverter::baseConvert('187556', 10, ['-', '-!', '-"', '-#', '-¤', '-%']); // outputs: -¤---¤-!-%-"
```

## Credits ##

This library is copyright 2013 - 2015 to Riikka Kalliomäki.

See LICENSE for license and copying information.
