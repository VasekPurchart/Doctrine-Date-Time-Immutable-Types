Doctrine DBAL DateTimeImmutable Types
=====================================

### Why do I want to use this?

All Doctrine date/time based types are using `DateTime` instances, which are mutable. This can lead very easily to breaking encapsulation and therefore bugs. For two reasons:

1) You accidentally modify a date when you are doing some computation on it:
```php
<?php

// created date might be modified
// even if it is not "supposed to" by the intentions of the creator
// (there is no set/modify method on the entity)
var_dump($logRow->getCreatedDate()); // 2015-01-01 00:00:00
$logRow->getCreatedDate()->modify('+14 days');
var_dump($logRow->getCreatedDate()); // 2015-01-15 00:00:00
```

2) Or you *do* intentionally try to update it, which fails because Doctrine will not see this:
```php
<?php

$product->getRenewDate()->modify('+1 year);
$entityManager->persist($product);
$entityManager->flush(); // no updates will be fired because Doctrine could not detect change (objects are compared by identity)
```

You can prevent this behaviour by returning a new instance (cloning) or using [`DateTimeImmutable`](http://php.net/manual/en/class.datetimeimmutable.php) (which returns a new instance when modified). `DateTimeImmutable` is available since PHP 5.5, but Doctrine has not adopted it yet, because it would introduce a [BC break](http://www.doctrine-project.org/jira/browse/DBAL-662). Maybe it will be supported in Doctrine 3.0, but until then you might want to use this package.

Installation
------------

> If you are using Symfony, you can use [`vasek-purchart/doctrine-date-time-immutable-types-bundle`](https://github.com/VasekPurchart/Doctrine-Date-Time-Immutable-Types-Bundle), which will take care of the integration.

Install package [`vasek-purchart/doctrine-date-time-immutable-types`](https://packagist.org/packages/vasek-purchart/doctrine-date-time-immutable-types) with [Composer](https://getcomposer.org/):

```
composer require vasek-purchart/doctrine-date-time-immutable-types
```

Then you just need to [register the types](http://doctrine-orm.readthedocs.org/en/latest/cookbook/custom-mapping-types.html) you want:
```php
<?php

use Doctrine\DBAL\Types\Type;

use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeTzImmutableType;
use VasekPurchart\Doctrine\Type\DateTimeImmutable\TimeImmutableType;

// use as date_immutable in mapping
Type::addType(DateImmutableType::NAME, DateImmutableType::class);
// use as datetime_immutable in mapping
Type::addType(DateTimeImmutableType::NAME, DateTimeImmutableType::class);
// use as datetimetz_immutable in mapping
Type::addType(DateTimeTzImmutableType::NAME, DateTimeTzImmutableType::class);
// use as time_immutable in mapping
Type::addType(TimeImmutableType::NAME, TimeImmutableType::class);
```

Or you might want to override some of the default types to work with `DateTimeImmutable` instead of `DateTime`:

```php
<?php

use Doctrine\DBAL\Types\Type;

use VasekPurchart\Doctrine\Type\DateTimeImmutable\DateTimeTzImmutableType;

// use as datetimetz in mapping
Type::overrideType(Type::DATETIMETZ, DateTimeTzImmutableType::class);
```
