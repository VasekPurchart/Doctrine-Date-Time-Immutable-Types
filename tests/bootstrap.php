<?php

declare(strict_types = 1);

namespace VasekPurchart\Doctrine\Type\DateTimeImmutable;

use Doctrine\DBAL\Types\Type;

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

Type::addType(DateImmutableType::NAME, DateImmutableType::class);
Type::addType(DateTimeImmutableType::NAME, DateTimeImmutableType::class);
Type::addType(DateTimeTzImmutableType::NAME, DateTimeTzImmutableType::class);
Type::addType(TimeImmutableType::NAME, TimeImmutableType::class);
