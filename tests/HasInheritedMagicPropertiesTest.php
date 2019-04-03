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

/**
 * Description of HasInheritedMagicPropertiesTest
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class HasInheritedMagicPropertiesTest extends \Aesonus\TestLib\BaseTestCase
{
    protected $testObj;

    protected function setUp(): void
    {
        $this->testObj = new TestFixtureInheritsProperties;
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
            ['my_own', 'string value'],
            ['testStringOrNullProperty', null],
            ['testFloatOrStringProperty', 'string value'],
            ['testFloatOrStringProperty', 3.14159],
            ['testStdClassOrNullWriteProperty', new \stdClass()],
            ['testMixedWriteProperty', 4.3]
        ];
    }
}
