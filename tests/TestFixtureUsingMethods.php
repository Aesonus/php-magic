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
 * You should write tests for all of your property methods!
 * @property string $property
 * @property-read int $read
 * @property-write bool $write
 * @author Aesonus <corylcomposinger at gmail.com>
 */
class TestFixtureUsingMethods
{
    use HasMagicProperties;

    public function __getRead()
    {

    }

    public function __issetRead()
    {

    }

    public function __setWrite($value)
    {

    }

    public function __unsetWrite()
    {

    }

    /**
     * This tells the self::magicSet() method to set the property using this method
     * @param mixed $value
     * @return void
     */
    public function __setProperty($value): void
    {

    }

    /**
     * This tells the self::magicUnset() method to unset the property using this method
     * @return void
     */
    public function __unsetProperty(): void
    {

    }

    /**
     * This tells the self::magicIsset() method to isset the property using this method
     *
     * @return bool
     */
    public function __issetProperty(): bool
    {

    }

    /**
     * This tells the self::magicGet() method to get the property using this method
     */
    public function __getProperty()
    {

    }
}
