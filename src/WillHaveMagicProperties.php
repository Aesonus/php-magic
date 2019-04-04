<?php
/*
 * This file is part of the php-magic package
 *
 *  (c) Cory Laughlin <corylcomposinger@gmail.com>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Aesonus\PhpMagic;

/**
 *
 * @author Aesonus <corylcomposinger at gmail.com>
 */
interface WillHaveMagicProperties
{
    public function magicGet($name);

    public function magicIsset($name);

    public function magicSet($name, $value);

    public function magicUnset($name);
}
