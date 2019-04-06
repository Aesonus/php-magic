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

/**
 * @property ?string $testStringOrNullProperty
 * @property float|string   $testFloatOrStringProperty
 * @property-read int $testIntReadProperty
 * @property-read callable|object $testCallableOrObjectReadProperty
 * @property-write \stdClass|null $testStdClassOrNullWriteProperty
 * @property-write mixed $testMixedWriteProperty
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class TestFixtureManyProperties
{
    use HasMagicProperties;
    use \Aesonus\PhpMagic\ImplementsMagicMethods;

    /**
     * @var array Use to access the protected properties on this object. For
     * testing purposes only.
     */
    public $propertyReferences;

    protected $testStringOrNullProperty;
    protected $testFloatOrStringProperty;
    protected $testIntReadProperty;
    protected $testCallableOrObjectReadProperty;
    protected $testStdClassOrNullWriteProperty;
    protected $testMixedWriteProperty;

    protected $notMagicProperty = 'not accessible';

    public function __construct()
    {
        $this->propertyReferences = [
            'testStringOrNullProperty' => &$this->testStringOrNullProperty,
            'testFloatOrStringProperty' => &$this->testFloatOrStringProperty,
            'testIntReadProperty' => &$this->testIntReadProperty,
            'testCallableOrObjectReadProperty' => &$this->testCallableOrObjectReadProperty,
            'testStdClassOrNullWriteProperty' => &$this->testStdClassOrNullWriteProperty,
            'testMixedWriteProperty' => &$this->testMixedWriteProperty,
        ];
    }
}
