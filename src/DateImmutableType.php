<?php

declare(strict_types = 1);

namespace VasekPurchart\Doctrine\Type\DateTimeImmutable;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DateImmutableType extends \Doctrine\DBAL\Types\DateType
{

	const NAME = 'date_immutable';

	public function getName(): string
	{
		return static::NAME;
	}

	/**
	 * @param \DateTimeImmutable|string|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return \DateTimeImmutable|null
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null || $value instanceof DateTimeImmutable) {
			return $value;
		}

		$dateTime = DateTimeImmutable::createFromFormat('!' . $platform->getDateFormatString(), $value);
		if ($dateTime === false) {
			throw \Doctrine\DBAL\Types\ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
		}

		return $dateTime;
	}

	/**
	 * @param \DateTimeInterface|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return string|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof DateTimeInterface) {
			return $value->format($platform->getDateFormatString());
		}

		if (!is_scalar($value)) {
			$value = sprintf('of type %s', gettype($value));
		}

		throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
