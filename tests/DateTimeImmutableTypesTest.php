<?php

namespace VasekPurchart\Doctrine\Type\DateTimeImmutable;

use DateTimeImmutable;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;

class DateTimeImmutableTypesTest extends \PHPUnit_Framework_TestCase
{

	public function typeClassesProvider()
	{
		return [
			[DateImmutableType::class],
			[DateTimeImmutableType::class],
			[DateTimeTzImmutableType::class],
			[TimeImmutableType::class],
		];
	}

	public function typesProvider()
	{
		return array_map(function ($typeClassRow) {
			return [DoctrineType::getType($typeClassRow[0]::NAME)];
		}, $this->typeClassesProvider());
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertBackAndForth(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$dateTime = new DateTimeImmutable('1970-01-01');
		$dbValue = $type->convertToDatabaseValue($dateTime, $platform);
		$newValue = $type->convertToPHPValue($dbValue, $platform);
		$this->assertInstanceOf(DateTimeImmutable::class, $newValue);
		$this->assertEquals($dateTime, $newValue);
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertNull(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->assertNull($type->convertToPHPValue(null, $platform));
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertDateTimeImmutableToPhpValue(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$now = new DateTimeImmutable();
		$this->assertSame($now, $type->convertToPHPValue($now, $platform));
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testInvalidString(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->setExpectedException(\Doctrine\DBAL\Types\ConversionException::class);
		$this->assertNull($type->convertToPHPValue('foobar', $platform));
	}

	/**
	 * @dataProvider typeClassesProvider
	 *
	 * @param string $typeClass
	 */
	public function testGetName($typeClass)
	{
		$this->assertSame($typeClass::NAME, DoctrineType::getType($typeClass::NAME)->getName());
	}

}
