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

    protected function __getRead()
    {
    }

    protected function __issetRead()
    {
    }

    protected function __setWrite($value)
    {
    }

    protected function __unsetWrite()
    {
    }

    /**
     * This tells the self::magicSet() method to set the property using this method
     * @param mixed $value
     * @return void
     */
    protected function __setProperty($value): void
    {
    }

    /**
     * This tells the self::magicUnset() method to unset the property using this method
     * @return void
     */
    protected function __unsetProperty(): void
    {
    }

    /**
     * This tells the self::magicIsset() method to isset the property using this method
     *
     * @return bool
     */
    protected function __issetProperty(): bool
    {
    }

    /**
     * This tells the self::magicGet() method to get the property using this method
     */
    protected function __getProperty()
    {
    }
}
