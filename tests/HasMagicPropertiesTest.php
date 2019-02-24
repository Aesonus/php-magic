<?php
/*
 * This file is part of the php-magic package
 *
 *  (c) Cory Laughlin <corylcomposinger@gmail.com>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Aesonus\Tests;

use Aesonus\TestLib\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

/**
 * Tests the magic properties class
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class HasMagicPropertiesTest extends BaseTestCase
{

    /**
     *
     * @var TestFixtureManyProperties|MockObject
     */
    public $testObj;

    protected function setUp(): void
    {

        $this->testObj = new TestFixtureManyProperties();
    }

    private function setProperty($property, $value)
    {
        $this->testObj->propertyReferences[$property] = $value;
    }

    /**
     * @test
     * @dataProvider validMagicGetDataProvider
     */
    public function magicGetGetsPropertyFromObj($property, $expected)
    {
        //Setup fixture
        $this->setProperty($property, $expected);
        $actual = $this->testObj->magicGet($property);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider invalidReadPropertyDataProvider
     */
    public function magicGetThrowsExceptionIfPropertyNotAccessible($property)
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Undefined property: Aesonus\Tests\TestFixtureManyProperties::$' . $property);
        $this->testObj->magicGet($property);
    }

    /**
     * Data Provider
     */
    public function invalidReadPropertyDataProvider()
    {
        return [
            ['notMagicProperty'],
            ['testStdClassOrNullWriteProperty']
        ];
    }

    /**
     * @test
     * @dataProvider validIssetPropertyDataProvider
     */
    public function magicIssetReturnsTrueIfPropertyIsSet($property, $value)
    {
        //Setup fixture
        $this->setProperty($property, $value);

        $this->assertTrue($this->testObj->magicIsset($property));
    }

    /**
     * Data Provider
     */
    public function validIssetPropertyDataProvider()
    {
        return [
            ['testStringOrNullProperty', 'string value'],
            ['testFloatOrStringProperty', 'string value'],
            ['testFloatOrStringProperty', 3.14159],
            ['testIntReadProperty', 23],
            ['testCallableOrObjectReadProperty', 'is_int'],
            ['testCallableOrObjectReadProperty', new stdClass()],
        ];
    }

    /**
     * @test
     * @dataProvider invalidReadPropertyDataProvider
     */
    public function magicIssetThrowsExceptionIfPropertyNotAccessible($property)
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Undefined property: Aesonus\Tests\TestFixtureManyProperties::$' . $property);
        $this->testObj->magicIsset($property);
    }

    /**
     * Data Provider
     */
    public function validMagicGetDataProvider()
    {
        return [
            ['testStringOrNullProperty', 'string value'],
            ['testStringOrNullProperty', null],
            ['testFloatOrStringProperty', 'string value'],
            ['testFloatOrStringProperty', 3.14159],
            ['testIntReadProperty', 23],
            ['testCallableOrObjectReadProperty', 'is_int'],
            ['testCallableOrObjectReadProperty', new stdClass()],
        ];
    }

    /**
     * @test
     * @dataProvider validSetPropertyDataProvider
     */
    public function magicSetSetsTheProperty($property, $expected)
    {
        $this->testObj->magicSet($property, $expected);

        $this->assertEquals($expected, $this->testObj->propertyReferences[$property]);
    }

    /**
     * Data Provider
     */
    public function validSetPropertyDataProvider()
    {
        return [
            ['testStringOrNullProperty', 'string value'],
            ['testStringOrNullProperty', null],
            ['testFloatOrStringProperty', 'string value'],
            ['testFloatOrStringProperty', 3.14159],
            ['testStdClassOrNullWriteProperty', new \stdClass()],
            ['testMixedWriteProperty', 4.3]
        ];
    }

    /**
     * @test
     * @dataProvider invalidSetPropertyTypeDataProvider
     */
    public function magicSetThrowsTypeErrorIfParameterTypeInvalid($property, $bad_type, $should_be)
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Aesonus\PhpMagic\HasMagicProperties::magicSet: Property "
            . "'$$property' must be of type(s)");
        $this->testObj->magicSet($property, $bad_type);
    }

    /**
     * Data Provider
     */
    public function invalidSetPropertyTypeDataProvider()
    {
        return [
            ['testStringOrNullProperty', 23, 'string|null'],
            ['testStringOrNullProperty', new \stdClass(), 'string|null'],
            ['testFloatOrStringProperty', 5, 'float|string'],
            ['testFloatOrStringProperty', false, 'float|string'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidSetPropertyDataProvider
     */
    public function magicSetThrowsErrorIfPropertyNotAccessible($property)
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Undefined property: Aesonus\Tests\TestFixtureManyProperties::$' . $property);
        $this->testObj->magicSet($property, 'non consequential value');
    }

    /**
     * @test
     * @dataProvider validUnSetPropertyDataProvider
     */
    public function magicUnsetUnsetsTheProperty($property)
    {
        $this->testObj->propertyReferences[$property] = 'not gonna be here for long';
        $this->testObj->magicUnset($property);
        $this->assertNull($this->testObj->propertyReferences[$property]);
    }

    /**
     * Data Provider
     */
    public function validUnSetPropertyDataProvider()
    {
        return [
            ['testStringOrNullProperty'],
            ['testStringOrNullProperty'],
            ['testStdClassOrNullWriteProperty'],
            ['testMixedWriteProperty'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidSetPropertyDataProvider
     */
    public function magicUnsetThrowsErrorIfPropertyNotAccessible($property)
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Undefined property: Aesonus\Tests\TestFixtureManyProperties::$' . $property);
        $this->testObj->magicUnset($property);
    }

    /**
     * Data Provider
     */
    public function invalidSetPropertyDataProvider()
    {
        return [
            ['testIntReadProperty'],
            ['notAProperty']
        ];
    }

    /**
     * @test
     */
    public function testInvalidPropertyTypeThrowsRuntimeException()
    {
        $testObj = new TestFixtureMalformedProperty;
        $this->expectException(\RuntimeException::class);
        $testObj->magicGet('name');
    }
}
