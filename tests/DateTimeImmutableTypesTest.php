<?php

namespace VasekPurchart\Doctrine\Type\DateTimeImmutable;

use DateTimeImmutable;
use stdClass;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;

class DateTimeImmutableTypesTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return string[][]
	 */
	public function typeClassesProvider()
	{
		return [
			[DateImmutableType::class],
			[DateTimeImmutableType::class],
			[DateTimeTzImmutableType::class],
			[TimeImmutableType::class],
		];
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]
	 */
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
		$this->expectException(\Doctrine\DBAL\Types\ConversionException::class);
		$this->assertNull($type->convertToPHPValue('foobar', $platform));
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertToDatabaseValueWithNull(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->assertNull($type->convertToDatabaseValue(null, $platform));
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertToDatabaseValueWithString(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->expectException(\Doctrine\DBAL\Types\ConversionException::class);
		$type->convertToDatabaseValue('foobar', $platform);
	}

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertToDatabaseValueWithObject(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->expectException(\Doctrine\DBAL\Types\ConversionException::class);
		$this->expectExceptionMessage('of type object');
		$type->convertToDatabaseValue(new stdClass(), $platform);
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

	/**
	 * @dataProvider typesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testRequiresSqlCommentHint(DoctrineType $type)
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		$this->assertTrue($type->requiresSQLCommentHint($platform));
	}

}
