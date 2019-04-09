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

    /**
     * The methods used by the TestFixtureUsingMethods object
     * @var array
     */
    public $magicByMethodsMethods = [
        '__setProperty',
        '__unsetProperty',
        '__getProperty',
        '__issetProperty',
        '__unsetWrite',
        '__setWrite',
        '__issetRead',
        '__getRead'
    ];

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
        $actual = $this->testObj->$property;
        $this->assertEquals($expected, $actual);
    }

    private function setUpMagicByMethods()
    {
        $this->testObj = $this->getMockBuilder(TestFixtureUsingMethods::class)
                ->setMethods($this->magicByMethodsMethods)->getMock();
    }

    private function expectNoMethodCalls()
    {
        foreach ($this->magicByMethodsMethods as $method) {
            $this->testObj->expects($this->never())->method($method);
        }
    }

    /**
     * @test
     * @dataProvider validMagicGetUsingMethodsDataProvider
     */
    public function magicGetCalls__getPropertyUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->testObj->expects($this->once())->method("__get" . ucfirst($property))->with();
        $this->testObj->magicGet($property);
    }

    /**
     * @test
     * @dataProvider invalidMagicGetUsingMethodsDataProvider
     */
    public function magicGetCallsNoMethodIfPropertyNotAccessibleUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->expectException(\Error::class);
        //Expect no method calls to any getter or setter
        $this->expectNoMethodCalls();
        $t = $this->testObj->magicGet($property);
    }

    /**
     * Data Provider
     */
    public function validMagicGetUsingMethodsDataProvider()
    {
        return [
            ['property'],
            ['read_property']
        ];
    }

    /**
     * Data Provider
     */
    public function invalidMagicGetUsingMethodsDataProvider()
    {
        return [
            ['nope'],
            ['write']
        ];
    }

    /**
     * @test
     * @dataProvider invalidReadPropertyDataProvider
     */
    public function magicGetThrowsExceptionIfPropertyNotAccessible($property)
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Undefined property: Aesonus\Tests\TestFixtureManyProperties::$' . $property);
        $this->testObj->$property;
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

        $this->assertTrue(isset($this->testObj->$property));
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
        isset($this->testObj->$property);
    }

    /**
     * @test
     * @dataProvider validMagicGetUsingMethodsDataProvider
     */
    public function magicIssetCalls__issetPropertyUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->testObj->expects($this->once())->method("__isset" . ucfirst($property))
            ->with()->willReturn(true);
        $this->testObj->magicIsset($property);
    }

    /**
     * @test
     * @dataProvider invalidMagicGetUsingMethodsDataProvider
     */
    public function magicIssetCallsNoMethodIfPropertyNotAccessibleUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->expectException(\Error::class);
        //Expect no method calls to any getter or setter
        $this->expectNoMethodCalls();
        $t = $this->testObj->magicIsset($property);
    }


    /**
     * @test
     * @dataProvider validSetPropertyDataProvider
     */
    public function magicSetSetsTheProperty($property, $expected)
    {
        $this->testObj->$property = $expected;

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
        $this->testObj->$property = $bad_type;
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
        $this->testObj->$property = 'non consequential value';
    }

    /**
     * @test
     * @dataProvider validMagicSetUsingMethodsDataProvider
     */
    public function magicSetCalls__setPropertyUsingMethods($property, $value)
    {
        $this->setUpMagicByMethods();
        $this->testObj->expects($this->once())->method("__set" . ucfirst($property))->with();
        $this->testObj->magicSet($property, $value);
    }

    /**
     * @test
     * @dataProvider invalidMagicSetUsingMethodsDataProvider
     */
    public function magicSetCallsNoMethodIfPropertyNotAccessibleUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->expectException(\Error::class);
        //Expect no method calls to any getter or setter
        $this->expectNoMethodCalls();
        $t = $this->testObj->magicSet($property, "doesn't matter");
    }

    /**
     * Data Provider
     */
    public function validMagicSetUsingMethodsDataProvider()
    {
        return [
            ['property', 'test string'],
            ['write', true]
        ];
    }

    /**
     * Data Provider
     */
    public function invalidMagicSetUsingMethodsDataProvider()
    {
        return [
            ['nope'],
            ['read']
        ];
    }

    /**
     * @test
     * @dataProvider validMagicSetUsingMethodsDataProvider
     */
    public function magicUnsetCalls__unsetPropertyUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->testObj->expects($this->once())->method("__unset" . ucfirst($property))->with();
        $this->testObj->magicUnset($property);
    }

    /**
     * @test
     * @dataProvider invalidMagicSetUsingMethodsDataProvider
     */
    public function magicUnsetCallsNoMethodIfPropertyNotAccessibleUsingMethods($property)
    {
        $this->setUpMagicByMethods();
        $this->expectException(\Error::class);
        //Expect no method calls to any getter or setter
        $this->expectNoMethodCalls();
        $t = $this->testObj->magicUnset($property);
    }

    /**
     * @test
     * @dataProvider validUnSetPropertyDataProvider
     */
    public function magicUnsetUnsetsTheProperty($property)
    {
        $this->testObj->propertyReferences[$property] = 'not gonna be here for long';
        unset($this->testObj->$property);
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
        unset($this->testObj->$property);
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
