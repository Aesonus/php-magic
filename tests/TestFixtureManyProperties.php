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

use Aesonus\PhpMagic\HasMagicProperties;
use Aesonus\PhpMagic\ImplementsMagicMethods;
use Aesonus\Tests\Fixtures\FixtureClassProperty;

/**
 * @property ?string $testStringOrNullProperty
 * @property float|string   $testFloatOrStringProperty
 * @property-read int $testIntReadProperty
 * @property-read callable|object $testCallableOrObjectReadProperty
 * @property-write FixtureClassProperty|null $testNamespacedClassOrNullWriteProperty
 * @property-write mixed $testMixedWriteProperty
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class TestFixtureManyProperties
{
    use HasMagicProperties;
    use ImplementsMagicMethods;

    /**
     * @var array Use to access the protected properties on this object. For
     * testing purposes only.
     */
    public $propertyReferences;

    protected $testStringOrNullProperty;
    protected $testFloatOrStringProperty;
    protected $testIntReadProperty;
    protected $testCallableOrObjectReadProperty;
    protected $testNamespacedClassOrNullWriteProperty;
    protected $testMixedWriteProperty;

    protected $notMagicProperty = 'not accessible';

    public function __construct()
    {
        $this->propertyReferences = [
            'testStringOrNullProperty' => &$this->testStringOrNullProperty,
            'testFloatOrStringProperty' => &$this->testFloatOrStringProperty,
            'testIntReadProperty' => &$this->testIntReadProperty,
            'testCallableOrObjectReadProperty' => &$this->testCallableOrObjectReadProperty,
            'testNamespacedClassOrNullWriteProperty' => &$this->testNamespacedClassOrNullWriteProperty,
            'testMixedWriteProperty' => &$this->testMixedWriteProperty,
        ];
    }
}
