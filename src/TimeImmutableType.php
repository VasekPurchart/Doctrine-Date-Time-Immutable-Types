<?php

namespace VasekPurchart\Doctrine\Type\DateTimeImmutable;

use DateTimeImmutable;
use DateTimeInterface;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class TimeImmutableType extends \Doctrine\DBAL\Types\TimeType
{

	const NAME = 'time_immutable';

	/**
	 * @return string
	 */
	public function getName()
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

		$dateTime = DateTimeImmutable::createFromFormat('!' . $platform->getTimeFormatString(), $value);
		if ($dateTime === false) {
			throw \Doctrine\DBAL\Types\ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getTimeFormatString());
		}

		return $dateTime;
	}

	/**
	 * @param \DateTimeInterface|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return string
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return $value;
		}

		if ($value instanceof DateTimeInterface) {
			return $value->format($platform->getTimeFormatString());
		}

		if (!is_scalar($value)) {
			$value = sprintf('of type %s', gettype($value));
		}

		throw \Doctrine\DBAL\Types\ConversionException::conversionFailed($value, $this->getName());
	}

	/**
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return boolean
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform)
	{
		return true;
	}

}
